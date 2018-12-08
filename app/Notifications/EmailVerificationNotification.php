<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

/**
 * 在类的申明里我们加上了 implements ShouldQueue，
 * ShouldQueue 这个接口本身没有定义任何方法，
 * 对于实现了 ShouldQueue 的邮件类 Laravel 会用将发邮件的操作放进队列里来实现异步发送；
 * greeting() 方法可以设置邮件的欢迎词；
 * subject() 方法用来设定邮件的标题；
 * line() 方法会在邮件内容里添加一行文字；
 * action() 方法会在邮件内容里添加一个链接按钮。
 * 这里就是激活链接，我们暂时把链接设成了主页，接下来我们来实现这个激活链接的逻辑。
 * Class EmailVerificationNotification
 * @package App\Notifications
 */
class EmailVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    // 我们只需要通过邮件通知，因此这里只需要一个 mail 即可
    public function via($notifiable)
    {
        return ['mail'];
    }

    // 发送邮件时会调用此方法来构建邮件内容，参数就是 App\Models\User 对象
    public function toMail($notifiable)
    {
        // 使用 Laravel 内置的 Str 类生成随机字符串的函数，参数就是要生成的字符串长度
        $token = Str::random(16);
        // 往缓存中写入这个随机字符串，有效时间为 30 分钟。
        Cache::set('email_verification_'.$notifiable->email, $token, 30);
        $url = route('email_verification.verify', ['email' => $notifiable->email, 'token' => $token]);
        return (new MailMessage)
            ->greeting($notifiable->name.'您好：')
            ->subject('注册成功，请验证您的邮箱')
            ->line('请点击下方链接验证您的邮箱')
            ->action('验证', $url);
    }

    public function toArray($notifiable)
    {
        return [];
    }
}