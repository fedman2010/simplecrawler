<?php

declare(strict_types=1);

namespace App\Services\Crawler;

use Illuminate\Http\Client\Response;

/**
 * Contains information about site page
 */
class ParsedPage
{
    public string $URL = "";
    public array $images = [];
    public array $internalLinks = [];
    public array $externalLinks = [];
    public string $title = "";
    public float $loadTime = 0;
    public int $status = 0;
    public int $wordNumber = 0;
    public string $html = "";
}
