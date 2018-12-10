<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\InternalException;

class ProductSku extends Model
{

    protected $fillable = ['title', 'description', 'price', 'stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // 减少库存

    /**
     * 为何使用 数据库查询构造器 $this->newQuery() ？
     * ORM 查询构造器的写操作只会返回 true 或者 false 代表 SQL 是否执行成功，
     * 而数据库查询构造器的写操作则会返回影响的行数。
     * 最终执行的 SQL 类似于 update product_skus set stock = stock - $amount where id = $id and stock >= $amount，
     * 这样可以保证不会出现执行之后 stock 值为负数的情况，也就避免了超卖的问题。
     * 而且我们用了数据库查询构造器，可以通过返回的影响行数来判断减库存操作是否成功，
     * 如果不成功说明商品库存不足。
     * 如果减库存失败则抛出异常，由于这块代码是在 DB::transaction() 中执行的，因此抛出异常时会触发事务的回滚，
     * 之前创建的 orders 和 order_items 记录都会被撤销。
     */
    public function decreaseStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('减库存不可小于0');
        }

        return $this->newQuery()->where('id', $this->id)->where('stock', '>=', $amount)->decrement('stock', $amount);
    }

    // 增加库存

    /**
     * 通过 increment() 方法来保证操作的原子性。
     * @param $amount
     * @throws InternalException
     */
    public function addStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('加库存不可小于0');
        }
        $this->increment('stock', $amount);
    }
}
