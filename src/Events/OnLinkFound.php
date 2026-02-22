<?php

namespace Tonsoo\SitemapGenerator\Events;

readonly class OnLinkFound extends CrawlerEvent
{
    public function __construct(
        public string $url,
        public string $link,
    ) { }
}