<?php

declare(strict_types=1);

use Tonsoo\SitemapGenerator\Extensions\SitemapExtension;
use Tonsoo\SitemapGenerator\Sitemap\SitemapGenerator;
use Tonsoo\SitemapGenerator\Sitemap\Writers\RotatingSitemapWriter;

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
        new SitemapExtension(
            generator: new SitemapGenerator(
                writer: new RotatingSitemapWriter(
                    directory: __DIR__ . DIRECTORY_SEPARATOR . 'sitemap',
                )
            )
        )
    )
    ->start('https://sancove.com.br/');