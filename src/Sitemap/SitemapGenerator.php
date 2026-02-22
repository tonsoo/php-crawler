<?php

namespace Tonsoo\PhpCrawler\Sitemap;

use Tonsoo\PhpCrawler\Sitemap\Entries\UrlEntry;
use Tonsoo\PhpCrawler\Sitemap\Writers\SitemapWriterInterface;

readonly class SitemapGenerator
{
    public function __construct(
        private SitemapWriterInterface $writer
    ) {}

    public function open(): void
    {
        $this->writer->open();
    }

    public function add(string $url, ?\DateTimeInterface $lastModified = null): void
    {
        $this->writer->add(
            new UrlEntry($url, $lastModified)
        );
    }

    public function close(): void
    {
        $this->writer->close();
    }
}