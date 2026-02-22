<?php

namespace Tonsoo\PhpCrawler\Extensions;

use Tonsoo\PhpCrawler\Crawler\Crawler;

interface CrawlerExtensionInterface
{
    public function subscribe(Crawler $crawler): void;
}