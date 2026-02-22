<?php

namespace Tonsoo\SitemapGenerator\Events;

class OnFinish extends CrawlerEvent
{
    public function __construct(
        public int $totalPages,
        public int $elapsedSeconds,
    ) { }
}