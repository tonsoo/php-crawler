# Sitemap Generator Crawler

A small, dependency-light PHP crawler that walks a site and generates XML sitemaps. It follows links, respects meta robots directives, and ships with a sitemap extension that can write a single sitemap or rotate into multiple files with an index.

## Requirements

- PHP 8.4+
- Extensions: `ext-dom`, `ext-curl`, `ext-xmlwriter`

## Installation

```bash
composer require tonsoo/sitemap-generator
```

## Quick Start

```php
<?php

use Tonsoo\SitemapGenerator\Extensions\SitemapExtension;
use Tonsoo\SitemapGenerator\Sitemap\SitemapGenerator;
use Tonsoo\SitemapGenerator\Sitemap\Writers\RotatingSitemapWriter;

require __DIR__ . '/vendor/autoload.php';

crawler()
    ->preserveHost()
    ->respectCanonical(false)
    ->maxPages(1000)
    ->subscribe(
        new SitemapExtension(
            generator: new SitemapGenerator(
                writer: new RotatingSitemapWriter(
                    directory: __DIR__ . '/sitemap'
                )
            )
        )
    )
    ->start('https://example.com');
```

This will crawl `https://example.com`, write `sitemap.xml` (or `sitemap-2.xml`, `sitemap-3.xml`, etc.), and produce a `sitemap-index.xml` once multiple sitemap files are created.

## Crawler Configuration

The crawler is configured via a fluent API on `Crawler`:

```php
crawler()
    ->displayCrawls(true)
    ->displayMemoryInfo(true)
    ->respectNoIndex(true)
    ->respectNoFollow(true)
    ->respectCanonical(true)
    ->preserveScheme(true)
    ->preserveHost(true)
    ->maxPages(5000)
    ->start('https://example.com');
```

### What these options do

- `displayCrawls(true)`: toggles crawl logging (currently not used by the built-in logger).
- `displayMemoryInfo(true)`: toggles memory logging (currently not used by the built-in logger).
- `respectNoIndex(true)`: honors `<meta name="robots" content="noindex">` (default: `true`).
- `respectNoFollow(true)`: honors `<meta name="robots" content="nofollow">` (default: `true`).
- `respectCanonical(true)`: uses the canonical URL for link resolution (default: `true`).
- `preserveScheme(true)`: stays on the same scheme (`http` vs `https`) (default: `true`).
- `preserveHost(true)`: stays on the same host (default: `true`).
- `maxPages(5000)`: stops after a page limit (default: `null` = unlimited).

## Sitemap Generation

### Single sitemap

```php
use Tonsoo\SitemapGenerator\Sitemap\SitemapGenerator;
use Tonsoo\SitemapGenerator\Sitemap\Writers\XmlSitemapWriter;
use Tonsoo\SitemapGenerator\Extensions\SitemapExtension;

crawler()
    ->subscribe(
        new SitemapExtension(
            generator: new SitemapGenerator(
                writer: new XmlSitemapWriter(
                    path: __DIR__ . '/sitemap/sitemap.xml'
                )
            )
        )
    )
    ->start('https://example.com');
```

### Rotating sitemap + index

```php
use Tonsoo\SitemapGenerator\Sitemap\SitemapGenerator;
use Tonsoo\SitemapGenerator\Sitemap\Writers\RotatingSitemapWriter;
use Tonsoo\SitemapGenerator\Extensions\SitemapExtension;

crawler()
    ->subscribe(
        new SitemapExtension(
            generator: new SitemapGenerator(
                writer: new RotatingSitemapWriter(
                    directory: __DIR__ . '/sitemap',
                    baseName: 'sitemap',
                    extension: 'xml',
                    maxUrls: 50000
                )
            )
        )
    )
    ->start('https://example.com');
```

Notes:

- `RotatingSitemapWriter` **requires the directory to already exist**.
- The index file is written only when more than one sitemap file is created.
- The index stores the sitemap filenames (relative paths), not absolute URLs.

## Events

You can subscribe to crawler events to observe or extend behavior:

```php
use Tonsoo\SitemapGenerator\Events\OnCrawled;
use Tonsoo\SitemapGenerator\Events\OnFinish;
use Tonsoo\SitemapGenerator\Events\OnLinkFound;
use Tonsoo\SitemapGenerator\Events\OnMismatchContent;
use Tonsoo\SitemapGenerator\Events\OnMissingHtmlBody;
use Tonsoo\SitemapGenerator\Events\OnStart;

crawler()
    ->onStart(fn (OnStart $event) => print("Starting\n"))
    ->onLinkFound(fn (OnLinkFound $event) => print("{$event->url} -> {$event->link}\n"))
    ->onCrawled(fn (OnCrawled $event) => print("Crawled {$event->page->uri}\n"))
    ->onMissingHtmlBody(fn (OnMissingHtmlBody $event) => print("No HTML: {$event->url}\n"))
    ->onMismatchContent(fn (OnMismatchContent $event) => print("Wrong content type: {$event->url}\n"))
    ->onFinish(fn (OnFinish $event) => print("Done: {$event->totalPages} pages\n"))
    ->start('https://example.com');
```

## Custom HTTP Client, Logger, and Analyzer

You can plug in your own implementations:

```php
use Tonsoo\SitemapGenerator\Http\HttpClientInterface;
use Tonsoo\SitemapGenerator\Logger\LoggerInterface;
use Tonsoo\SitemapGenerator\Analysis\PageAnalyzerInterface;

crawler()
    ->httpClient(new YourHttpClient())
    ->logger(new YourLogger())
    ->pageAnalyzer(new YourAnalyzer())
    ->start('https://example.com');
```

Defaults:

- HTTP client: `CurlHttpClient` (follows redirects, 4s connect/total timeout, custom UA string).
- Logger: `ConsoleLogger` (timestamps to stdout).
- Analyzer: `DomDocumentPageAnalyzer` (DOM + XPath).

Interfaces to implement:

- `HttpClientInterface::fetch(string $url): Result`
- `LoggerInterface::log(string $message): void`
- `PageAnalyzerInterface::analyze(Result $result, bool $respectNoIndex, bool $respectNoFollow): PageAnalysis`

## Error Handling

If `maxPages` is set and the crawler reaches the limit, it throws `LimitExceededException` **after** finishing the crawl loop:

```php
use Tonsoo\SitemapGenerator\Crawler\Exception\LimitExceededException;

try {
    crawler()->maxPages(100)->start('https://example.com');
} catch (LimitExceededException $e) {
    // handle limit reached
}
```

## Crawling Behavior

The crawler only processes pages that return an HTML body with a `text/html` content type. If a page has no HTML body or a non-HTML content type, it is skipped and the corresponding event is emitted.

The crawler collects links from `<a href="...">` elements and normalizes them. It will:

- Resolve relative URLs against the current page
- Drop fragments (the `#...` part)
- Ignore non-HTTP(S) schemes
- Optionally restrict links by host and scheme
- Optionally respect `noindex` / `nofollow` meta tags (from `<meta name="robots">`)
- Use canonical URLs when enabled

This crawler does **not** parse `robots.txt`.

## Example Script

See `examples/crawler.php` for a full working example.
