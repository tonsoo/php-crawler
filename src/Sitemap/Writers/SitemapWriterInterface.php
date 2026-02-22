<?php

namespace Tonsoo\SitemapGenerator\Sitemap\Writers;

use Tonsoo\SitemapGenerator\Sitemap\Entries\UrlEntry;

interface SitemapWriterInterface
{
    public function open(): void;

    public function add(UrlEntry $entry): void;

    public function close(): void;
}