<?php

declare(strict_types=1);

namespace App\Services\Crawler\Parsers;

use App\Services\Crawler\Helpers\LinkHelper;
use App\Services\Crawler\Interfaces\ParserInterface;
use App\Services\Crawler\ParsedPage;
use App\Services\Crawler\SitePage;
use DOMDocument;
use DOMXPath;

/**
 * Parser based on PHP extention DOM 
 */
class DOMParser implements ParserInterface
{
    protected DOMDocument $dom;
    protected LinkHelper $helper;
    protected SitePage $page;

    public function init(SitePage $page, string $host): void
    {
        $this->page = $page;
        $this->helper = new LinkHelper($host, $page->URL);

        $this->dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($page->getHtml());
        libxml_use_internal_errors(false);
    }

    public function process(): ParsedPage
    {
        $result = new ParsedPage();

        $links = $this->getLinks();
        $result->URL = $this->page->URL;
        $result->internalLinks = $this->helper->getInternalLinks($links);
        $result->externalLinks = $this->helper->getExternalLinks($links);
        $result->images = $this->getImagesSources();
        $result->title = $this->getTitle();
        $result->wordNumber = $this->countWords();
        $result->loadTime = $this->page->loadTime;
        $result->status = $this->page->status;
        $result->html = $this->page->getHtml();

        return $result;
    }

    /**
     * Find all links on a page and return them
     * 
     * @return array<int,string>
     */
    protected function getLinks(): array
    {
        $links = [];
        foreach ($this->dom->getElementsByTagName('a') as $node) {
            $links[] = $node->getAttribute('href');
        }

        return array_filter($links);
    }

    /**
     * Return image sources on a page
     * 
     * @return array<int,string>
     */
    protected function getImagesSources(): array
    {
        $links = [];

        foreach ($this->dom->getElementsByTagName('img') as $node) {
            $link = $node->getAttribute('src');
            if ($link)
                $links[] = $link;
        }

        $links = array_unique($links);

        return array_filter($links);
    }

    protected function getTitle(): string
    {
        $nodeList = $this->dom->getElementsByTagName('title');

        if ($nodeList->count() == 0) {
            return "";
        }

        $title = $nodeList->item(0)?->textContent;

        return trim($title);
    }

    protected function countWords(): int
    {
        $xpath = new DOMXPath($this->dom);
        $text = "";

        foreach ($xpath->query('//text()') as $node) {
            if ($node->nodeName == "#text" && trim($node->textContent)) {
                $text .= " " . trim($node->textContent);
            }
        }

        $words = array_filter(explode(" ", $text));

        return count($words);
    }
}
