<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler
{
    /**
     * 注意：类异常并不是因为我们系统本身的问题导致的，不会影响我们系统的运行，
     * 如果大量此类日志打印到日志文件里反而会影响我们去分析真正有问题的异常，因此需要屏蔽这个行为。
     * 当一个异常被触发时，Laravel 会去检查这个异常的类型是否在 $dontReport 属性中定义了，
     * 如果有则不会打印到日志文件中。
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        InvalidRequestException::class,
        CouponCodeUnavailableException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * @param Exception $exception
     * @return mixed|void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }
}
