<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use App\Listeners\RegisteredListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\OrderPaid;
use App\Listeners\UpdateProductSoldCount;
use App\Listeners\SendOrderPaidMail;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     * 关联事件和监听器
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        Registered::class => [
            RegisteredListener::class,
        ],
        OrderPaid::class => [
            UpdateProductSoldCount::class,
            SendOrderPaidMail::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
