<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

/**
 * 系统内部异常
 * 比如连接数据库失败，
 * 对于此类异常我们需要有限度地告知用户发生了什么，
 * 但又不能把所有信息都暴露给用户（比如连接数据库失败的信息里会包含数据库地址和账号密码），
 * 因此我们需要传入两条信息，一条是给用户看的，另一条是打印到日志中给开发人员看的。
 *
 * Class InternalException
 * @package App\Exceptions
 */
class InternalException extends Exception
{
    protected $msgForUser;

    public function __construct(string $message, string $msgForUser = '系统内部错误', int $code = 500)
    {
        parent::__construct($message, $code);
        $this->msgForUser = $msgForUser;
    }

    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['msg' => $this->msgForUser], $this->code);
        }

        return view('pages.error', ['msg' => $this->msgForUser]);
    }
}
