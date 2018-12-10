<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;

/**
 * 在创建订单的同时我们减去了对应商品 SKU 的库存，
 * 恶意用户可以通过下大量的订单又不支付来占用商品库存，让正常的用户因为库存不足而无法下单。
 * 因此我们需要有一个关闭未支付订单的机制，
 * 当创建订单之后一定时间内没有支付，将关闭订单并退回减去的库存。
 *
 * 当我们的系统触发了一个延迟任务时，
 * Laravel 会用当前时间加上任务的延迟时间计算出任务应该被执行的时间戳，
 * 然后将这个时间戳和任务信息序列化之后存入队列，
 * Laravel 的队列处理器会不断查询并执行队列中满足预计执行时间等于或早于当前时间的任务。
 *
 * Class CloseOrder
 * @package App\Jobs
 */
// 代表这个类需要被放到队列中执行，而不是触发时立即执行
class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order, $delay)
    {
        $this->order = $order;
        // 设置延迟的时间，delay() 方法的参数代表多少秒之后执行
        $this->delay($delay);
    }

    // 定义这个任务类具体的执行逻辑
    // 当队列处理器从队列中取出任务时，会调用 handle() 方法
    public function handle()
    {
        // 判断对应的订单是否已经被支付
        // 如果已经支付则不需要关闭订单，直接退出
        if ($this->order->paid_at) {
            return;
        }
        // 通过事务执行 sql
        \DB::transaction(function() {
            // 将订单的 closed 字段标记为 true，即关闭订单
            $this->order->update(['closed' => true]);
            // 循环遍历订单中的商品 SKU，将订单中的数量加回到 SKU 的库存中去
            foreach ($this->order->items as $item) {
                $item->productSku->addStock($item->amount);
            }
        });
    }
}