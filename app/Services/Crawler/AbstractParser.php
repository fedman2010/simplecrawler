<?php

namespace App\Services\Crawler;

/**
 * Abstract class for parsing web site page
 */
abstract class AbstractParser
{
    public function __construct(protected SitePage $page)
    {
    }

    public function process()
    {
        $this->page->internalLinks = $this->getInteranlLinks();
        $this->page->images = $this->getImagesSources();
        $this->page->externalLinks = $this->getExternalLinks();
        $this->page->internalLinks = $this->getInteranlLinks();
        $this->page->title = $this->getTitle();
        $this->page->wordNumber = $this->countWords();
    }

    abstract protected function getInteranlLinks(): array;
    abstract protected function getImagesSources(): array;
    abstract protected function getExternalLinks(): array;
    abstract protected function getInternalLinks(): array;
    abstract protected function getTitle(): string;
    abstract protected function countWords(): int;
}
