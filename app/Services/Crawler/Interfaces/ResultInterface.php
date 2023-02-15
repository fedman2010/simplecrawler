<?php

declare(strict_types=1);

namespace App\Services\Crawler\Interfaces;


/**
 * Holds statistics of a crawling results. 
 */
interface ResultInterface
{
    public function getPageCount(): int;
    public function getImageCount(): int;
    public function getExternalLinksCount(): int;
    public function getInternalLinksCount(): int;
    public function getAverageTitleLength(): int;
    public function getAverageWordCount(): int;
    public function getAverageLoadTime(): float;
}
