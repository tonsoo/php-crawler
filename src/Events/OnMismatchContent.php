<?php

namespace Tonsoo\SitemapGenerator\Events;

class OnMismatchContent extends CrawlerEvent
{
    public function __construct(
        public string $url,
    ) { }
}