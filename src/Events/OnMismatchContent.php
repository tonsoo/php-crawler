<?php

namespace Tonsoo\SitemapGenerator\Events;

readonly class OnMismatchContent extends CrawlerEvent
{
    public function __construct(
        public string $url,
        public string $contentType,
    ) { }
}