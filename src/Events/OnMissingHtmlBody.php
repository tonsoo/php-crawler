<?php

namespace Tonsoo\PhpCrawler\Events;

readonly class OnMissingHtmlBody extends CrawlerEvent
{
    public function __construct(
        public string $url,
    ) { }
}