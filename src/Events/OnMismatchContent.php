<?php

namespace Tonsoo\PhpCrawler\Events;

readonly class OnMismatchContent extends CrawlerEvent
{
    public function __construct(
        public string $url,
        public string $contentType,
    ) { }
}