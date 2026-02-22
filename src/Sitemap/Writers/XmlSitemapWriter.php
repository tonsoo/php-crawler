<?php

namespace Tonsoo\PhpCrawler\Sitemap\Writers;

use Tonsoo\PhpCrawler\Sitemap\Entries\UrlEntry;

class XmlSitemapWriter implements SitemapWriterInterface
{
    private \XMLWriter $xml;

    public function __construct(
        private readonly string $path
    )
    {
        $this->xml = new \XMLWriter();
    }

    public function open(): void
    {
        $this->xml->openURI($this->path);
        $this->xml->startDocument('1.0', 'UTF-8');
        $this->xml->startElement('urlset');
        $this->xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $this->xml->writeAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');
    }

    public function add(UrlEntry $entry): void
    {
        echo "Adding {$entry->url}\n\n";

        $this->xml->startElement('url');

        $this->xml->writeElement('loc', $entry->url);

        if ($entry->lastModified) {
            $this->xml->writeElement(
                'lastmod',
                $entry->lastModified->format('Y-m-d')
            );
        }

        if ($entry->changeFrequency) {
            $this->xml->writeElement('changefreq', $entry->changeFrequency);
        }

        if ($entry->priority !== null) {
            $this->xml->writeElement('priority', (string) $entry->priority);
        }

        $this->xml->endElement();
    }

    public function close(): void
    {
        $this->xml->endElement();
        $this->xml->endDocument();
        $this->xml->flush();
    }
}