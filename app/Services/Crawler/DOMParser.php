<?php

namespace App\Services\Crawler;

use DOMDocument;
use DOMXPath;

/**
 * Parser based on PHP extention DOM 
 */
class DOMParser extends AbstractParser
{
    protected DOMDocument $dom;
    protected string $host;

    public function __construct(protected SitePage $page)
    {
        parent::__construct($page);

        $this->dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($page->getHtml());
        libxml_use_internal_errors(false);
        $this->host = parse_url($this->page->URL, PHP_URL_HOST);
    }


    protected function getInteranlLinks(): array
    {
        $paths = [];
        $nodeList = $this->dom->getElementsByTagName('a');

        foreach ($nodeList as $node) {
            $URL = parse_url($node->getAttribute('href'));

            if (
                !$URL
                || isset($URL['host']) && $URL['host'] != $this->host
                || empty($URL['path'])
                || $URL['path'] == "/"
            ) {
                continue;
            }

            $paths[] = $URL['path'];
        }

        $paths = array_unique($paths);
        array_walk($paths, function (&$path, $key) {
            $url = parse_url($this->page->URL);
            $scheme = ($url['scheme'] ?? "https") . "://";
            $port = ":" . ($url['port'] ?? "");
            $path = "{$scheme}{$this->host}{$port}{$path}";
        });

        return $paths;
    }

    protected function getImagesSources(): array
    {
        $links = [];
        $nodeList = $this->dom->getElementsByTagName('img');

        foreach ($nodeList as $node) {
            $links[] = $node->getAttribute('src');
        }

        $links = array_unique($links);

        return $links;
    }

    protected function getExternalLinks(): array
    {
        $paths = [];
        $nodeList = $this->dom->getElementsByTagName('a');

        foreach ($nodeList as $node) {
            $URL = parse_url($node->getAttribute('href'));

            if (
                !$URL
                || empty($URL['host'])
                || $URL['host'] == $this->host
                || empty($URL['path'])
            ) {
                continue;
            }

            $paths[] = $URL['host'] . $URL['path'];
        }

        $paths = array_unique($paths);

        return $paths;
    }

    protected function getInternalLinks(): array
    {
        $paths = [];
        $nodeList = $this->dom->getElementsByTagName('a');

        foreach ($nodeList as $node) {
            $URL = parse_url($node->getAttribute('href'));

            if (
                !$URL
                || isset($URL['host']) && $URL['host'] != $this->host
                || empty($URL['path'])
                || $URL['path'] == "/"
            ) {
                continue;
            }

            $paths[] = $URL['path'];
        }

        $paths = array_unique($paths);

        return $paths;
    }

    protected function getTitle(): string
    {
        $nodeList = $this->dom->getElementsByTagName('title');

        if ($nodeList->count() == 0) {
            return 0;
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
}
