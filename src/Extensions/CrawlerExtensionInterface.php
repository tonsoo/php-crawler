<?php

namespace Tonsoo\SitemapGenerator\Extensions;

use Tonsoo\SitemapGenerator\Crawler\Crawler;

interface CrawlerExtensionInterface
{
    public function subscribe(Crawler $crawler): void;
}