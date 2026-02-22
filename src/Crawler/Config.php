<?php

namespace Tonsoo\PhpCrawler\Crawler;

use Tonsoo\PhpCrawler\Analysis\DomDocumentPageAnalyzer;
use Tonsoo\PhpCrawler\Analysis\PageAnalyzerInterface;
use Tonsoo\PhpCrawler\Http\CurlHttpClient;
use Tonsoo\PhpCrawler\Http\HttpClientInterface;
use Tonsoo\PhpCrawler\Logger\ConsoleLogger;
use Tonsoo\PhpCrawler\Logger\LoggerInterface;

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
