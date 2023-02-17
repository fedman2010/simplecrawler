<?php

declare(strict_types=1);

namespace App\Services\Crawler;

use App\Services\Crawler\Interfaces\ResultInterface;

/**
 * Holds statistics of a crawling results. 
 */
class Result implements ResultInterface
{
    protected int $imageNumber = 0;
    protected int $externalLinkNumber = 0;
    protected int $internalLinkNumber = 0;
    protected int $averageTitleLength = 0;
    protected int $averageWordNumber = 0;
    protected float $averageLoadTime = 0;

    public function __construct(protected array $pages, protected int $pageCount)
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

    public function getImageCount(): int
    {
        return $this->imageNumber;
    }

    public function getExternalLinksCount(): int
    {
        return $this->externalLinkNumber;
    }

    public function getInternalLinksCount(): int
    {
        return $this->internalLinkNumber;
    }

    public function getAverageTitleLength(): int
    {
        return $this->averageTitleLength;
    }

    public function getAverageWordCount(): int
    {
        return $this->averageWordNumber;
    }

    public function getAverageLoadTime(): float
    {
        return $this->averageLoadTime;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }

    /**
     * @return array<ParsedPage>
     */
    public function getPages(): array
    {
        return $this->pages;
    }
}
