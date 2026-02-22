<?php

namespace Tonsoo\PhpCrawler\Data;

use League\Uri\Uri;

final readonly class Page
{
    public function __construct(
        public Uri    $uri,
        public Result $crawlResult,
        public array  $links,
        public Robots $robots,
    ) {}
}