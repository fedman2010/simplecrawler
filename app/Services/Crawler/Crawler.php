<?php

namespace App\Services\Crawler;

use GuzzleHttp\Exception\TransferException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Contains logic of crawling web site pages
 */
class Crawler
{
    /**
     * Array of SitePage objects
     */
    public array $pages = [];

    /**
     * Array of site links
     */
    protected array $links = [];

    /**
     * Number of web pages to crawl
     */
    protected int $crawlCount = 6;

    /**
     * Count successfully crawled links
     */
    protected int $count = 0;

    public function __construct(protected string $siteURL)
    {
        $this->links[] = $this->siteURL;
    }

    /**
     * All crawling logic is in this method.
     * 
     * @return bool
     */
    public function process(): bool
    {
        $link = array_shift($this->links);

        try {
            $response = Http::get($link);
        } catch (TransferException $e) {
            Log::warning($e->getMessage());
            return false;
        }

        $this->count++;
        $sitePage = new SitePage($response, $link);

        if ($response->ok()) {
            $parser = new DOMParser($sitePage);
            $parser->process();
            $this->links = array_unique(array_merge($this->links, $sitePage->internalLinks));
        }

        $this->pages[] = $sitePage;

        if ($this->count < $this->crawlCount && count($this->links) > 0) {
            $this->process();
        }

        return true;
    }

    /**
     * Return number of crawled pages
     * 
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }
}
