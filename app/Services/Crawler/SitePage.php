<?php

namespace App\Services\Crawler;

use Illuminate\Http\Client\Response;

/**
 * Contains information about site page
 */
class SitePage
{
    public array $images = [];
    public array $internalLinks = [];
    public array $externalLinks = [];
    public string $title = "";
    public float $loadTime = 0;
    public int $status;

    /**
     * Word count on a site page
     */
    public int $wordNumber = 0;

    protected string $html = "";

    public function __construct(Response $response, public string $URL) {
        $this->html = $response->body();
        $this->status = $response->status();
        $this->loadTime = $response->transferStats->getTransferTime();
    }

    public function getHtml(): string
    {
        return $this->html;
    }
}
