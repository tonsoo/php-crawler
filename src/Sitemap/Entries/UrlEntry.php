<?php

namespace Tonsoo\PhpCrawler\Sitemap\Entries;

use DateTimeInterface;

final readonly class UrlEntry
{
    public function __construct(
        public string $url,
        public ?DateTimeInterface $lastModified = null,
        public ?string $changeFrequency = null,
        public ?float $priority = null,
    ) {}
}