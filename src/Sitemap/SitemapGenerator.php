<?php

namespace Tonsoo\SitemapGenerator\Sitemap;

use Tonsoo\SitemapGenerator\Sitemap\Entries\UrlEntry;
use Tonsoo\SitemapGenerator\Sitemap\Writers\SitemapWriterInterface;

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