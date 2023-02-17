<?php

declare(strict_types=1);

namespace App\Services\Crawler\Helpers;

class LinkHelper
{

    public function __construct(protected string $host, protected string $pageURL)
    {
    }

    /**
     * Sort out internal links and return them as an array
     * 
     * @param array<string> all links
     * @return  array<string> internal links
     */
    public function getInternalLinks(array $links): array
    {
        $subDomainLinks = [];
        $domainLinks = [];

        foreach ($links as $link) {
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
                $url = parse_url($this->pageURL);
                $scheme = ($url['scheme'] ?? "https") . "://";
                $port = empty($url['port']) ? "" : ":" . $url['port'];
                $host = $url['host'];
                $link = "{$scheme}{$host}{$port}{$link}";
            }

            unset($link);
        }

        return array_unique(array_merge($subDomainLinks, $domainLinks));
    }

    /**
     * Sort out external links and return them as an array
     * 
     * @param array<string> all links
     * @return  array<string> external links
     */
    public function getExternalLinks(array $links): array
    {
        $res = [];

        foreach ($links as $link) {
            $URL = parse_url($link);

            if (
                !$URL
                || empty($URL['host'])
                || !$this->isAnotherDomain($URL['host'])
            ) {
                continue;
            }

            $res[] = $link;
        }

        $res = array_unique($res);

        return $res;
    }

    public function isSubDomain(string $domain): bool
    {
        return str_ends_with($domain, $this->host);
    }

    public function isSameDomain(string $domain): bool
    {
        return $domain === $this->host;
    }

    public function isAnotherDomain(string $domain): bool
    {
        return !$this->isSubDomain($domain);
    }
}
