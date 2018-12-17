<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Yansongda\Pay\Pay;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /**
         * $this->app->singleton() 往服务容器中注入一个单例对象，
         * 第一次从容器中取对象时会调用回调函数来生成对应的对象并保存到容器中，
         * 之后再去取的时候直接将容器中的对象返回。
         * app()->environment() 获取当前运行的环境，
         * 线上环境会返回 production。对于支付宝，
         * 如果项目运行环境不是线上环境，则启用开发模式，并且将日志级别设置为 DEBUG。
         * 由于微信支付没有开发模式，所以仅仅将日志级别设置为 DEBUG。
         */
        // 往服务容器中注入一个名为 alipay 的单例对象
        $this->app->singleton('alipay', function () {
            $config = config('pay.alipay');
            //注意：回调地址必须是完整的带有域名的 URL，不可以是相对路径。
            //使用 route() 函数生成的 URL 默认就是带有域名的完整地址。
//            $config['notify_url'] = 'http://requestbin.fullcontact.com/x9m4tvx9';//服务器端测试回调地址
            $config['notify_url'] = route('payment.alipay.notify');//服务器端回调地址
            $config['return_url'] = route('payment.alipay.return');//前端回调地址
            // 判断当前项目运行环境是否为线上环境
            if (app()->environment() !== 'production') {
                $config['mode'] = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }

            // 调用 Yansongda\Pay 来创建一个支付宝支付对象
            return Pay::alipay($config);
        });

        $this->app->singleton('wechat_pay', function () {
            $config = config('pay.wechat');
            $config['notify_url'] = route('payment.wechat.notify');//服务器端回调地址
            if (app()->environment() !== 'production') {
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }

            // 调用 Yansongda\Pay 来创建一个微信支付对象
            return Pay::wechat($config);
        });
    }
}
