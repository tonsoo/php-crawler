<?php

use Tonsoo\SitemapGenerator\Events\OnMismatchContent;
use Tonsoo\SitemapGenerator\Extensions\SitemapExtension;
use Tonsoo\SitemapGenerator\Sitemap\SitemapGenerator;
use Tonsoo\SitemapGenerator\Sitemap\Writers\RotatingSitemapWriter;
use Tonsoo\SitemapGenerator\Sitemap\Writers\XmlSitemapWriter;

require dirname(__DIR__) . '/vendor/autoload.php';

crawler()
    ->preserveHost()
    ->displayCrawls()
    ->displayMemoryInfo()
    ->respectCanonical(false)
    ->respectNoIndex()
    ->respectNoFollow()
    ->maxPages(10)
    ->subscribe(
        // you can add multiple extensions
        new SitemapExtension(
            generator: new SitemapGenerator(
                // a sitemap writer that rotates using sitemap-index when it reaches 50k pages
                writer: new RotatingSitemapWriter(
                    directory: __DIR__ . DIRECTORY_SEPARATOR . 'sitemap',
                )

                // a simple xml writer (RotatingSitemapWriter uses it under the hood)
                // writer: new XmlSitemapWriter(
                //     path: __DIR__ . DIRECTORY_SEPARATOR . 'sitemap/sitemap.xml',
                // )
            )
        )
    )
    // you can add listeners to events
    ->onMismatchContent(fn (OnMismatchContent $event) => print("Page {$event->url} has content {$event->contentType}"))
    ->start('https://alysson-thoaldo.com.br');