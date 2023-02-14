<?php

namespace App\Services\Crawler\Interfaces;

interface CrawlerInterface
{
    public function process(string $siteURL): bool;
    public function getCount(): int;
    public function getResult(): ResultInterface;
}
