<?php

declare(strict_types=1);

namespace App\Services\Crawler\Providers;

use App\Services\Crawler\Crawler;
use App\Services\Crawler\Interfaces\CrawlerInterface;
use App\Services\Crawler\Interfaces\ParserInterface;
use App\Services\Crawler\Parsers\DOMParser;
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
        $this->app->bind(ParserInterface::class, function ($app) {
            return new DOMParser();
        });

        $this->app->bind(CrawlerInterface::class, function ($app) {
            return new Crawler(
                config('crawler.page_count'),
                $app->make(ParserInterface::class)
            );
        });
    }
}
