<?php

namespace Tonsoo\PhpCrawler\Events;

readonly class OnFinish extends CrawlerEvent
{
    public function __construct(
        public int $totalPages,
        public int $elapsedSeconds,
    ) { }
}