<?php

namespace Tonsoo\SitemapGenerator\Events;

class OnMissingHtmlBody extends CrawlerEvent
{
    public function __construct(
        public string $url,
    ) { }
}