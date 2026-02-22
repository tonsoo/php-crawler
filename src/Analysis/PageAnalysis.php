<?php

namespace Tonsoo\SitemapGenerator\Analysis;

use Tonsoo\SitemapGenerator\Data\Robots;

final readonly class PageAnalysis
{
    /**
     * @param list<string> $links
     */
    public function __construct(
        public Robots $robots,
        public string $canonicalUrl,
        public array  $links,
    ) {
    }
}
