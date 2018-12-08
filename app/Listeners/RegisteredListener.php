<?php

namespace App\Listeners;

use App\Notifications\EmailVerificationNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * 我们希望用户在注册完成之后系统就会发送激活邮件，而不是让用户自己去请求激活邮件。
 * 我们可以通过 Laravel 的事件系统来完成这个功能，
 * 用户注册完成之后会触发一个 Illuminate\Auth\Events\Registered 事件，
 * 我们可以创建一个这个事件的监听器（Listener）来发送邮件。 (监听也可以配置成异步执行)
 * 注意：监听器创建完成之后还需要在 EventServiceProvider 中将事件和监听器关联起来才能生效！
 *
 * Class RegisteredListener
 * @package App\Listeners
 */

// implements ShouldQueue 让这个监听器异步执行
class RegisteredListener implements ShouldQueue
{
    // 当事件被触发时，对应该事件的监听器的 handle() 方法就会被调用
    public function handle(Registered $event)
    {
        // 获取到刚刚注册的用户
        $user = $event->user;
        // 调用 notify 发送通知
        $user->notify(new EmailVerificationNotification());
    }
}