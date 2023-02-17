<?php

declare(strict_types=1);

namespace App\Services\Crawler;

use Illuminate\Http\Client\Response;

/**
 * Contains information about site page
 */
class SitePage
{
    public float $loadTime = 0;
    public int $status;
    protected string $html = "";

    public function __construct(Response $response, public string $URL)
    {
        $this->html = $response->body();
        $this->status = $response->status();
        $this->loadTime = $response->transferStats->getTransferTime();
    }

    public function getHtml(): string
    {
        return $this->html;
    }
}
