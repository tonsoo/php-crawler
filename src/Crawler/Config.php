<?php

namespace Tonsoo\SitemapGenerator\Crawler;

use Tonsoo\SitemapGenerator\Analysis\DomDocumentPageAnalyzer;
use Tonsoo\SitemapGenerator\Analysis\PageAnalyzerInterface;
use Tonsoo\SitemapGenerator\Http\CurlHttpClient;
use Tonsoo\SitemapGenerator\Http\HttpClientInterface;
use Tonsoo\SitemapGenerator\Logger\ConsoleLogger;
use Tonsoo\SitemapGenerator\Logger\LoggerInterface;

final class Config
{
    public function __construct(
        public bool $displayMemoryInfo = false,
        public bool $displayCrawls = false,
        public bool $respectNoIndex = true,
        public bool $respectNoFollow = true,
        public bool $respectCanonical = true,
        public bool $preserveScheme = true,
        public bool $preserveHost = true,
        public ?int $maxPages = null,
        public LoggerInterface $logger = new ConsoleLogger(),
        public PageAnalyzerInterface $pageAnalyzer = new DomDocumentPageAnalyzer(),
        public HttpClientInterface   $httpClient = new CurlHttpClient()
    ) {}
}
