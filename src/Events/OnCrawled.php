<?php

namespace Tonsoo\SitemapGenerator\Events;

use Tonsoo\SitemapGenerator\Data\Page;

readonly class OnCrawled extends CrawlerEvent
{
    public function __construct(
        public Page $page,
    ) { }
}