<?php

namespace Tonsoo\PhpCrawler\Sitemap\Writers;

use Tonsoo\PhpCrawler\Sitemap\Entries\UrlEntry;

interface SitemapWriterInterface
{
    public function open(): void;

    public function add(UrlEntry $entry): void;

    public function close(): void;
}