<?php

declare(strict_types=1);

namespace App\Services\Crawler\Interfaces;

use App\Services\Crawler\SitePage;

/**
 * Interface for parsing web site page
 */
interface ParserInterface
{
    public function process(SitePage $page): void;
}
