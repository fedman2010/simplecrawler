<?php

declare(strict_types=1);

namespace App\Services\Crawler\Interfaces;

interface CrawlerInterface
{
    public function process(string $siteURL): bool;
    public function getResult(): ResultInterface;
}
