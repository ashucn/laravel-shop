<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use Carbon\Carbon;

/**
 * 关于 Service 模式
 * Service 模式将 PHP 的商业逻辑写在对应责任的 Service 类里，解決 Controller 臃肿的问题。
 * 并且符合 SOLID 的单一责任原则，
 * 购物车的逻辑由 CartService 负责，而不是 CartController ，
 * 控制器是调度中心，编码逻辑更加清晰。
 * 后面如果我们有 API 或者其他会使用到购物车功能的需求，
 * 也可以直接使用 CartService ，代码可复用性大大增加。
 * 再加上 Service 可以利用 Laravel 提供的依赖注入机制，
 * 大大提高了 Service 部分代码的可测试性，程序的健壮性越佳。
 *
 * Class OrderService
 * @package App\Services
 */
class OrderService
{

    public function store(User $user, UserAddress $address, $remark, $items)
    {

        // 开启一个数据库事务
        /**
         * 在回调函数里的所有 SQL 写操作都会被包含在这个事务里，
         * 如果回调函数抛出异常则会自动回滚这个事务，否则提交事务。
         * 用这个方法可以帮我们节省不少代码。
         */
        $order = \DB::transaction(function () use ($user, $address, $remark, $items) {
            // 更新此地址的最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            // 创建一个订单
            $order = new Order([
                'address'      => [ // 将地址信息放入订单中
                    'address'       => $address->full_address,
                    'zip'           => $address->zip,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark'       => $remark,
                'total_amount' => 0,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();

            $totalAmount = 0;
            // 遍历用户提交的 SKU
            foreach ($items as $data) {
                $sku = ProductSku::find($data['sku_id']);
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
            $skuIds = collect($items)->pluck('sku_id')->all();
            app(CartService::class)->remove($skuIds);

            return $order;
        });

        // 这里我们直接使用 dispatch 函数
        dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }
}