<?php

namespace Tonsoo\SitemapGenerator\Events;

class OnLinkFound extends CrawlerEvent
{
    public function __construct(
        public string $url,
        public string $link,
    ) { }
}