<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Models\Order;
use Carbon\Carbon;
use App\Jobs\CloseOrder;
use App\Exceptions\InvalidRequestException;

class OrdersController extends Controller
{

    /**
     * 思路
     * 在事务里先创建了一个订单，把当前用户设为订单的用户，然后把传入的地址数据快照进 address 字段。
     * 然后遍历传入的商品 SKU 及其数量，
     * $order->items()->make() 方法可以新建一个关联关系的对象（也就是 OrderItem）但不保存到数据库，
     * 这个方法等同于 $item = new OrderItem(); $item->order()->associate($order);。
     * 然后根据所有的商品单价和数量求得订单的总价格，更新到刚刚创建的订单的 total_amount 字段。
     * 最后使用 Laravel 提供的 collect() 辅助函数快速取得所有 SKU ID，然后将本次订单中的商品 SKU 从购物车中删除。
     *
     * @param OrderRequest $request
     * @return mixed
     */
    public function store(OrderRequest $request)
    {
        $user  = $request->user();
        // 开启一个数据库事务
        /**
         * 在回调函数里的所有 SQL 写操作都会被包含在这个事务里，
         * 如果回调函数抛出异常则会自动回滚这个事务，否则提交事务。
         * 用这个方法可以帮我们节省不少代码。
         */
        $order = \DB::transaction(function () use ($user, $request) {
            $address = UserAddress::find($request->input('address_id'));
            // 更新此地址的最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            // 创建一个订单
            $order   = new Order([
                'address'      => [ // 将地址信息放入订单中
                    'address'       => $address->full_address,
                    'zip'           => $address->zip,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark'       => $request->input('remark'),
                'total_amount' => 0,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();

            $totalAmount = 0;
            $items       = $request->input('items');
            // 遍历用户提交的 SKU
            foreach ($items as $data) {
                $sku  = ProductSku::find($data['sku_id']);
                // 创建一个 OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price'  => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount += $sku->price * $data['amount'];
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new InvalidRequestException('该商品库存不足');
                }
            }

            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount]);

            // 将下单的商品从购物车中移除
            $skuIds = collect($items)->pluck('sku_id');
            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();

            return $order;
        });
//        触发删除未支付订单
        $this->dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }
}