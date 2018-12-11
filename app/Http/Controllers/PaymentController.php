<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;

/**
 * 可以通过 requestbin 服务来帮我们捕获服务器端回调的数据。
 * requestbin 是一个免费开源的网站，任何人都可以在上面申请一个专属的 URL（通常有效期 48 小时），
 * 对这个 URL 的任何类型的请求都会被记录下来，URL 的创建者可以看到请求的具体信息，
 *
 * 包含请求时间、请求头、请求具体数据等。
 * Class PaymentController
 * @package App\Http\Controllers
 */
class PaymentController extends Controller
{
    public function payByAlipay(Order $order, Request $request)
    {
        // 判断订单是否属于当前用户
        $this->authorize('own', $order);
        // 订单已支付或者已关闭
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }

        // 调用支付宝的网页支付
        return app('alipay')->web([
            'out_trade_no' => $order->no, // 订单编号，需保证在商户端不重复
            'total_amount' => $order->total_amount, // 订单金额，单位元，支持小数点后两位
            'subject'      => '支付 Laravel Shop 的订单：'.$order->no, // 订单标题
        ]);
    }

    // 前端回调页面
    public function alipayReturn()
    {
        /**
         * 用于校验提交的参数是否合法，支付宝的前端跳转会带有数据签名，
         * 通过校验数据签名可以判断参数是否被恶意用户篡改。
         * 同时该方法还会返回解析后的参数。
         */
        // 校验提交的参数是否合法
        try {
            app('alipay')->verify();
        } catch (\Exception $e) {
            return view('pages.error', ['msg' => '数据不正确']);
        }

        return view('pages.success', ['msg' => '付款成功']);
    }

    // 服务器端回调
    public function alipayNotify()
    {

        $data = app('alipay')->verify();
        \Log::debug('Alipay notify', $data->all());//由于服务器端的请求我们无法看到返回值，使用 dd 就不行了，所以需要通过日志的方式来保存。

        // 如果订单状态不是成功或者结束，则不走后续的逻辑
        // 所有校验状态：https://docs.open.alipay.com/59/103672
        if(!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        // $data->out_trade_no 拿到订单流水号，并在数据库中查询
        $order = Order::where('no', $data->out_trade_no)->first();
        // 正常来说不太可能出现支付了一笔不存在的订单，这个判断只是加强系统健壮性。
        if (!$order) {
            return 'fail';
        }
        // 如果这笔订单的状态已经是已支付
        if ($order->paid_at) {
            // 返回数据给支付宝
            return app('alipay')->success();
        }

        $order->update([
            'paid_at'        => Carbon::now(), // 支付时间
            'payment_method' => 'alipay', // 支付方式
            'payment_no'     => $data->trade_no, // 支付宝订单号
        ]);

        return app('alipay')->success();
    }
}
