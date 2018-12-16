<?php

namespace App\Http\Controllers;

use App\Exceptions\CouponCodeUnavailableException;
use App\Models\CouponCode;

/**
 * 前台 优惠券的查询功能
 * abort() 方法可以直接中断我们程序的运行，接受的参数会变成 Http 状态码返回。
 * 在这里如果用户输入的优惠码不存在或者是没有启用我们就返回 404 给用户。
 * Class CouponCodesController
 * @package App\Http\Controllers
 */
class CouponCodesController extends Controller
{
    public function show($code)
    {
        // 判断优惠券是否存在
        if (!$record = CouponCode::where('code', $code)->first()) {
            throw new CouponCodeUnavailableException('优惠券不存在！');
        }

        $record->checkAvailable();

        return $record;
    }
}