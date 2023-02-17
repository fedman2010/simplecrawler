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
     * @var array<ParsedPage>
     */
    protected array $pages = [];

    /**
     * @var array<string>
     */
    protected array $links = [];

    /**
     * Count successfully crawled links
     * 
     * @var int
     */
    protected int $count = 0;

    /**
     * Domain name of a web site to crawl
     * 
     * @var string|null
     */
    protected ?string $host;

    public function __construct(
        protected int $crawlCount = 1,
        protected ParserInterface $parser
    ) {
    }

    /**
     * Start crawl process for provided $siteURL.
     * Return false if can't reach web site.
     * 
     * @param string $siteURL
     * @return bool
     */
    public function process(string $siteURL): bool
    {
        $this->links[] = $siteURL;
        $this->host = parse_url($siteURL, PHP_URL_HOST);

        if (!$this->host || !$this->processPage()) {
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
            $this->parser->init($sitePage, $this->host);
            $parsedPage = $this->parser->process();
            $this->links = array_unique(array_merge($this->links, $parsedPage->internalLinks));
        }

        $this->pages[] = $parsedPage;

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
