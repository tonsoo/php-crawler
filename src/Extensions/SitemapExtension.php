<?php

namespace Tonsoo\SitemapGenerator\Extensions;

use Tonsoo\SitemapGenerator\Crawler\Crawler;
use Tonsoo\SitemapGenerator\Events\OnCrawled;
use Tonsoo\SitemapGenerator\Sitemap\SitemapGenerator;

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