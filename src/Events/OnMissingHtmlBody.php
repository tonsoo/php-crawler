<?php

namespace Tonsoo\SitemapGenerator\Events;

readonly class OnMissingHtmlBody extends CrawlerEvent
{
    public function __construct(
        public string $url,
    ) { }
}