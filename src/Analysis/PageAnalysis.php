<?php

namespace Tonsoo\PhpCrawler\Analysis;

use Tonsoo\PhpCrawler\Data\Robots;

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
