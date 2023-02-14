<?php

namespace App\Services\Crawler;

use App\Services\Crawler\Interfaces\CrawlerInterface;
use Illuminate\Support\ServiceProvider;

class CrawlerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CrawlerInterface::class, function ($app) {
            return new Crawler(config('crawler.page_count'));
        });
    }
}
