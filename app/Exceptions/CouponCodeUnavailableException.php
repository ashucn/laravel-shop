<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Exception;

/**
 * 然我们在提交订单之前就已经检查过一次优惠码，但是提交时需要再次检查，因
 * 为有可能在用户检查优惠码和提交的时间空档中优惠券被其他人兑完了，或者是运营人员修改了优惠码规则。
 * 由于这个异常属于用户触发的业务异常，因此不需要记录在日志里，把它配到 ExceptionHandler 的 $dontReport 属性里：
 * Class CouponCodeUnavailableException
 * @package App\Exceptions
 */
class CouponCodeUnavailableException extends Exception
{

    public function __construct($message, int $code = 403)
    {
        parent::__construct($message, $code);
    }

    // 当这个异常被触发时，会调用 render 方法来输出给用户
    public function render(Request $request)
    {
        // 如果用户通过 Api 请求，则返回 JSON 格式的错误信息
        if ($request->expectsJson()) {
            return response()->json(['msg' => $this->message], $this->code);
        }

        // 否则返回上一页并带上错误信息
        return redirect()->back()->withErrors(['coupon_code' => $this->message]);
    }
}
