<?php

declare(strict_types=1);

namespace App\Services\Crawler;

use App\Services\Crawler\Interfaces\CrawlerInterface;
use App\Services\Crawler\Interfaces\ParserInterface;
use App\Services\Crawler\Interfaces\ResultInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Contains logic of crawling web site pages
 */
class Crawler implements CrawlerInterface
{
    /**
     * Array of SitePage objects
     * 
     * @var array<SitePage>
     */
    protected array $pages = [];

    /**
     * Array of links
     * 
     * @var array<string>
     */
    protected array $links = [];

    /**
     * Count successfully crawled links
     */
    protected int $count = 0;

    public function __construct(
        protected int $crawlCount = 1,
        protected ParserInterface $parser
    ) {
    }

    /**
     * Start crawl process for provided $siteURL. Return false
     * if can't reach web site
     * 
     * @param string $siteURL
     * @return bool
     */
    public function process(string $siteURL): bool
    {
        $this->links[] = $siteURL;

        if ($this->processPage() === false) {
            return false;
        }

        while ($this->count < $this->crawlCount && count($this->links) > 0) {
            $this->processPage();
        }

        return true;
    }

    /**
     * Get and parse one web site page at a time
     * 
     * @return bool
     */
    public function processPage(): bool
    {
        $link = array_shift($this->links);

        try {
            $response = Http::get($link);
        } catch (Throwable $e) {
            Log::warning($e->getMessage(), ['URL' => $link]);
            return false;
        }

        $this->count++;
        $sitePage = new SitePage($response, $link);

        if ($response->ok()) {
            $this->parser->process($sitePage);
            $this->links = array_unique(array_merge($this->links, $sitePage->internalLinks));
        }

        $this->pages[] = $sitePage;

        return true;
    }

    /**
     * Return results of a crawling 
     * 
     * @return ResultInterface
     */
    public function getResult(): ResultInterface
    {
        return new Result($this->pages, $this->count);
    }
}
