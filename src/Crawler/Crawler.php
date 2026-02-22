<?php

namespace Tonsoo\SitemapGenerator\Crawler;

use Carbon\Carbon;
use InvalidArgumentException;
use League\Uri\Uri;
use Tonsoo\SitemapGenerator\Analysis\PageAnalysis;
use Tonsoo\SitemapGenerator\Analysis\PageAnalyzerInterface;
use Tonsoo\SitemapGenerator\Crawler\Exception\LimitExceededException;
use Tonsoo\SitemapGenerator\Data\Page;
use Tonsoo\SitemapGenerator\Data\Result;
use Tonsoo\SitemapGenerator\Events\HasEvents;
use Tonsoo\SitemapGenerator\Events\OnCrawled;
use Tonsoo\SitemapGenerator\Events\OnFinish;
use Tonsoo\SitemapGenerator\Events\OnLinkFound;
use Tonsoo\SitemapGenerator\Events\OnMismatchContent;
use Tonsoo\SitemapGenerator\Events\OnMissingHtmlBody;
use Tonsoo\SitemapGenerator\Events\OnStart;
use Tonsoo\SitemapGenerator\Http\HttpClientInterface;
use Tonsoo\SitemapGenerator\Logger\LoggerInterface;

final class Crawler
{
    use HasEvents;

    private Config $config;

    public function __construct()
    {
        $this->config = new Config();
    }

    public function displayMemoryInfo(bool $value = true): self
    {
        $this->config->displayMemoryInfo = $value;
        return $this;
    }

    public function displayCrawls(bool $value = true): self
    {
        $this->config->displayCrawls = $value;
        return $this;
    }

    public function respectNoIndex(bool $value = true): self
    {
        $this->config->respectNoIndex = $value;
        return $this;
    }

    public function respectNoFollow(bool $value = true): self
    {
        $this->config->respectNoFollow = $value;
        return $this;
    }

    public function respectCanonical(bool $value = true): self
    {
        $this->config->respectCanonical = $value;
        return $this;
    }

    public function preserveScheme(bool $value = true): self
    {
        $this->config->preserveScheme = $value;
        return $this;
    }

    public function preserveHost(bool $value = true): self
    {
        $this->config->preserveHost = $value;
        return $this;
    }

    public function maxPages(int $max): self
    {
        $this->config->maxPages = $max;
        return $this;
    }

    public function logger(LoggerInterface $logger): self
    {
        $this->config->logger = $logger;
        return $this;
    }

    public function pageAnalyzer(PageAnalyzerInterface $analyzer): self
    {
        $this->config->pageAnalyzer = $analyzer;
        return $this;
    }

    public function httpClient(HttpClientInterface $httpClient): self
    {
        $this->config->httpClient = $httpClient;
        return $this;
    }

    public function start(string $url): void
    {
        $this->config->logger->log('Starting event');

        $uri = Uri::parse($url);
        if ($uri == null) {
            throw new InvalidArgumentException("The provided URL '{$url}' is not valid");
        }

        $start = Carbon::now();
        $this->dispatch(new OnStart());

        $visited = $this->crawl($uri);

        $elapsedSeconds = $start->diffInSeconds(Carbon::now());

        $this->dispatch(new OnFinish($visited, (int) $elapsedSeconds));

        $maxPages = $this->config->maxPages;
        if ($maxPages !== null && $visited >= $maxPages) {
            throw LimitExceededException::pages($maxPages);
        }
    }

    /**
     * Crawls all pages and returns the amount of pages found and crawled
     * @param Uri $uri
     * @return int
     */
    private function crawl(Uri $uri): int
    {
        $maxPages = $this->config->maxPages;
        $visited = [];
        $toVisit = [ "{$uri->normalize()}" => true ];

        do {
            $keys = array_keys($toVisit);

            // currentUrl is always normalized
            $currentUrl = $keys[0];
            $currentUri = Uri::parse($currentUrl);

            $this->config->logger->log("Preparing to visit page: $currentUrl");

            unset($toVisit[$currentUrl]);

            if (isset($visited[$currentUrl])) {
                $this->config->logger->log("Page '$currentUrl' was already visited\n");
                continue;
            }

            // mark url as visited
            $visited[$currentUrl] = true;

            $content = $this->fetchUriContent($currentUri);

            $analysis = $this->config->pageAnalyzer->analyze(
                result: $content,
                respectNoIndex: $this->config->respectNoIndex,
                respectNoFollow: $this->config->respectNoFollow
            );

            $links = $this->findContentLinks($analysis);
            foreach ($links as $link) {
                if (isset($toVisit[$link]) || isset($visited[$link])) {
                    $this->config->logger->log("Link '{$link}' already visited");
                    continue;
                }

                $this->config->logger->log("Found link '{$link}'");
                $this->dispatch(new OnLinkFound($currentUrl, $link));

                // link url is already normalized
                $toVisit[$link] = true;
            }

            $this->dispatch(
                new OnCrawled(new Page(
                    uri: $currentUri,
                    crawlResult: $content,
                    links: $links,
                    robots: $analysis->robots,
                ))
            );

            $this->config->logger->log("Finished crawling page '$currentUrl'");
        } while (! empty($toVisit) && ($maxPages == null || count($visited) < $maxPages));

        return count($visited);
    }

    private function fetchUriContent(Uri $uri): ?Result
    {
        $url = $uri->normalize()->toString();

        $result = $this->config->httpClient->fetch($url);

        if (!$result->hasHtmlBody()) {
            $this->dispatch(new OnMissingHtmlBody($url));
            $this->config->logger->log("Page '$url' has not html body");
            return null;
        }

        if (!$result->isHtmlContentType()) {
            $this->dispatch(new OnMismatchContent($url, $result->contentType));
            $this->config->logger->log("Page '$url' is not text/html");
            return null;
        }

        return $result;
    }

    /**
     * @param PageAnalysis $analysis
     * @return array<string>
     */
    private function findContentLinks(PageAnalysis $analysis): array
    {
        if (!$analysis->robots->follow) {
            return [];
        }
        $analysisUri = Uri::parse($analysis->canonicalUrl);

        $links = [];
        foreach ($analysis->links as $link) {
            $linkUri = Uri::parse($link);
            if ($linkUri == null) {
                continue;
            }

            if ($this->config->preserveHost) {
                if ($linkUri->getHost() != $analysisUri->getHost()) {
                    continue;
                }
            }

            if ($this->config->preserveScheme) {
                if ($linkUri->getScheme() != $analysisUri->getScheme()) {
                    continue;
                }
            }

            $normalized = $linkUri->normalize()->toString();
            if (isset($links[$normalized])) {
                continue;
            }

            $links[$normalized] = true;
        }

        return array_keys($links);
    }
}
