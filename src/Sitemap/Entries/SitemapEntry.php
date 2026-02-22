<?php

namespace Tonsoo\SitemapGenerator\Sitemap\Entries;

final readonly class SitemapEntry
{
    public function __construct(
        public string $location,
        public ?string $lastmod = null
    ) {}
}