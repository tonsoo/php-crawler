<?php

namespace Tonsoo\PhpCrawler\Sitemap;

use Tonsoo\PhpCrawler\Sitemap\Entries\SitemapEntry;

class SitemapIndexGenerator
{
    /**
     * @var array<SitemapEntry> $sitemaps
     */
    private array $sitemaps = [];

    public function add(SitemapEntry $entry): void
    {
        $this->sitemaps[] = $entry;
    }

    public function write(string $path): void
    {
        $xml = new \XMLWriter();
        $xml->openURI($path);
        $xml->startDocument('1.0', 'UTF-8');

        $xml->startElement('sitemapindex');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($this->sitemaps as $sitemap) {
            /** @var SitemapEntry $sitemap */
            $xml->startElement('sitemap');
            $xml->writeElement('loc', $sitemap->location);

            if ($sitemap->lastmod) {
                $xml->writeElement('lastmod', $sitemap->lastmod);
            }

            $xml->endElement();
        }

        $xml->endElement();
        $xml->endDocument();
        $xml->flush();
    }
}