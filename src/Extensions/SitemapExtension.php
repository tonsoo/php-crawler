<?php

namespace Tonsoo\PhpCrawler\Extensions;

use Tonsoo\PhpCrawler\Crawler\Crawler;
use Tonsoo\PhpCrawler\Events\OnCrawled;
use Tonsoo\PhpCrawler\Sitemap\SitemapGenerator;

readonly class SitemapExtension implements CrawlerExtensionInterface
{
    public function __construct(
        private SitemapGenerator $generator
    ) {}

    public function subscribe(Crawler $crawler): void
    {
        $crawler
            ->onStart(fn () => $this->generator->open())
            ->onCrawled(fn (OnCrawled $event) => $this->generator->add($event->page->uri->toString()))
            ->onFinish(fn () => $this->generator->close());
    }
}