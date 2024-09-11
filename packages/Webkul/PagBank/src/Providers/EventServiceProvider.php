<?php

namespace Webkul\PagBank\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Webkul\Theme\ViewRenderEventManager;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('bagisto.shop.layout.body.after', static function (ViewRenderEventManager $viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('pagbank::checkout.onepage.pagbank-smart-button');
        });

        Event::listen('sales.invoice.save.after', 'Webkul\PagBank\Listeners\Transaction@saveTransaction');
    }
}
