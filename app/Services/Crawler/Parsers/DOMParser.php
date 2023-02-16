<?php

declare(strict_types=1);

namespace App\Services\Crawler\Parsers;

use App\Services\Crawler\Interfaces\ParserInterface;
use App\Services\Crawler\SitePage;
use DOMDocument;
use DOMXPath;

/**
 * Parser based on PHP extention DOM 
 */
class DOMParser implements ParserInterface
{
    protected DOMDocument $dom;
    protected string $host;
    protected SitePage $page;

    public function process(SitePage $page): void
    {
        $this->page = $page;
        $this->host = parse_url($this->page->URL, PHP_URL_HOST);

        //prepare PHP extention DOM
        $this->dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($page->getHtml());
        libxml_use_internal_errors(false);

        //parse
        $this->page->internalLinks = $this->getInternalLinks();
        $this->page->images = $this->getImagesSources();
        $this->page->externalLinks = $this->getExternalLinks();
        $this->page->title = $this->getTitle();
        $this->page->wordNumber = $this->countWords();
    }


    protected function getInternalLinks(): array
    {
        $subDomainLinks = [];
        $domainLinks = [];

        foreach ($this->dom->getElementsByTagName('a') as $node) {
            $link = $node->getAttribute('href');
            $URL = parse_url($link);
            if (!$URL)
                continue;
            if (empty($URL['host']) || $this->isSameDomain($URL['host'])) {
                $domainLinks[] = $link;
            } elseif ($this->isSubDomain($URL['host'])) {
                $subDomainLinks[] = $link;
            }
        }

        foreach ($domainLinks as $key => &$link) {
            $parsedLink = parse_url($link);
            if (empty($parsedLink['path']) || $parsedLink['path'] == "/") {
                unset($domainLinks[$key]);
                continue;
            }
            if (empty($parsedLink['host'])) {
                $url = parse_url($this->page->URL);
                $scheme = ($url['scheme'] ?? "https") . "://";
                $port = empty($url['port']) ? "" : ":" . $url['port'];
                $host = $url['host'];
                $link = "{$scheme}{$host}{$port}{$link}";
            }
            unset($link);
        }

        return array_unique(array_merge($subDomainLinks, $domainLinks));
    }

    protected function getImagesSources(): array
    {
        $links = [];

        foreach ($this->dom->getElementsByTagName('img') as $node) {
            $links[] = $node->getAttribute('src');
        }

        $links = array_unique($links);

        return $links;
    }

    protected function getExternalLinks(): array
    {
        $paths = [];

        foreach ($this->dom->getElementsByTagName('a') as $node) {
            $URL = parse_url($node->getAttribute('href'));

            if (
                !$URL
                || empty($URL['host'])
                || !$this->isAnotherDomain($URL['host'])
            ) {
                continue;
            }

            $paths[] = $URL['host'] . ($URL['path'] ?? "");
        }

        $paths = array_unique($paths);

        return $paths;
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
        $nodeList = $this->dom->getElementsByTagName('body');

        if ($nodeList->count() == 0) {
            return 0;
        }

        $xpath = new DOMXPath($this->dom);
        $textnodes = $xpath->query('//text()');
        $text = "";

        foreach ($textnodes as $node) {
            if ($node->nodeName == "#text" && trim($node->textContent)) {
                $text .= " " . trim($node->textContent);
            }
        }

        $words = explode(" ", $text);

        return count($words);
    }

    protected function isSubDomain(string $domain): bool
    {
        return $domain !== $this->host && str_ends_with($domain, $this->host);
    }

    protected function isSameDomain(string $domain): bool
    {
        return $domain === $this->host;
    }

    protected function isAnotherDomain(string $domain): bool
    {
        return !$this->isSameDomain($domain) && !$this->isSubDomain($domain);
    }
}
