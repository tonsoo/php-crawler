<?php

namespace Tonsoo\PhpCrawler\Events;

use Tonsoo\PhpCrawler\Data\Page;

readonly class OnCrawled extends CrawlerEvent
{
    public function __construct(
        public Page $page,
    ) { }
}