<?php

declare(strict_types=1);

namespace App\Services\Crawler\Interfaces;

use App\Services\Crawler\ParsedPage;
use App\Services\Crawler\SitePage;

/**
 * Interface for parsing web site page
 */
interface ParserInterface
{
    public function init(SitePage $page, string $host): void;
    public function process(): ParsedPage;
}
