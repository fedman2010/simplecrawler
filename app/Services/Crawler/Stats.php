<?php

namespace App\Services\Crawler;

use Illuminate\Support\Collection;

/**
 * Holds statistics of a crawling results. 
 */
class Stats
{
    public int $imageNumber = 0;
    public int $externalLinkNumber = 0;
    public int $internalLinkNumber = 0;
    public int $averageTitleLength = 0;
    public int $averageWordNumber = 0;
    public float $averageLoadTime = 0;

    public function __construct(public array $pages, public int $pageCount)
    {
        foreach ($pages as $page) {
            $this->imageNumber += count($page->images);
            $this->externalLinkNumber += count($page->externalLinks);
            $this->internalLinkNumber += count($page->internalLinks);
            $this->averageTitleLength += strlen($page->title);
            $this->averageLoadTime += $page->loadTime;
            $this->averageWordNumber += $page->wordNumber;
        }
        $this->averageTitleLength = (int)($this->averageTitleLength / $this->pageCount);
        $this->averageLoadTime = $this->averageLoadTime / $this->pageCount;
        $this->averageWordNumber = (int)($this->averageWordNumber / $this->pageCount);
    }
}
