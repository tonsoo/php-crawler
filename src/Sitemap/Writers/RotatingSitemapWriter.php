<?php

namespace Tonsoo\SitemapGenerator\Sitemap\Writers;

use InvalidArgumentException;
use Tonsoo\SitemapGenerator\Sitemap\Entries\SitemapEntry;
use Tonsoo\SitemapGenerator\Sitemap\Entries\UrlEntry;
use Tonsoo\SitemapGenerator\Sitemap\SitemapIndexGenerator;

class RotatingSitemapWriter implements SitemapWriterInterface
{
    private int $urlCount = 0;
    private int $fileIndex = 1;
    private ?XmlSitemapWriter $currentWriter = null;
    private readonly SitemapIndexGenerator $indexGenerator;

    public function __construct(
        private readonly string $directory,
        private readonly string $baseName = 'sitemap',
        private readonly string $extension = 'xml',
        private readonly int $maxUrls = 50000,
    )
    {
        if (!is_dir($this->directory)) {
            throw new InvalidArgumentException('Directory "' . $this->directory . '" does not exist.');
        }

        $this->indexGenerator = new SitemapIndexGenerator();
    }

    public function open(): void
    {
        $this->rotate();
    }

    public function add(UrlEntry $entry): void
    {
        if ($this->urlCount >= $this->maxUrls) {
            $this->rotate();
        }

        $this->currentWriter->add($entry);
        $this->urlCount++;
    }

    public function close(): void
    {
        if ($this->currentWriter !== null) {
            $this->currentWriter->close();
        }

        if ($this->fileIndex > 2) {
            $this->indexGenerator->write(
                $this->directory . '/sitemap-index.xml'
            );
        }
    }

    private function rotate(): void
    {
        if ($this->currentWriter !== null) {
            $this->currentWriter->close();
        }

        $index = $this->fileIndex <= 1 ? '' : "-{$this->fileIndex}";

        $filename = "{$this->baseName}{$index}.{$this->extension}";
        $path = $this->directory . '/' . $filename;

        $this->currentWriter = new XmlSitemapWriter($path);
        $this->currentWriter->open();

        $this->indexGenerator->add(
            new SitemapEntry(
                location: $filename,
                lastmod: date('Y-m-d')
            )
        );

        $this->urlCount = 0;
        $this->fileIndex++;
    }
}