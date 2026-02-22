<?php

namespace Tonsoo\PhpCrawler\Analysis;

use InvalidArgumentException;
use League\Uri\Uri;
use Tonsoo\PhpCrawler\Data\Result;
use Tonsoo\PhpCrawler\Data\Robots;

class DomDocumentPageAnalyzer implements PageAnalyzerInterface
{
    public function analyze(Result $result, bool $respectNoIndex = true, bool $respectNoFollow = true): PageAnalysis
    {
        if (empty($result->html)) {
            throw new InvalidArgumentException("Html cannot be empty for page analysis");
        }

        $dom = new \DOMDocument();
        @$dom->loadHTML($result->html);

        $xpath = new \DOMXPath($dom);

        $robots = $this->extractRobotsDirectives($xpath);
        $canonical = $this->extractCanonicalUrl($xpath, $result->effectiveUrl);
        $links = $this->extractLinks($xpath, $result->effectiveUrl);

        $robots = new Robots(
            index: $respectNoIndex ? $robots->index : true,
            follow: $respectNoFollow ? $robots->follow : true,
        );

        return new PageAnalysis($robots, $canonical, $links);
    }

    private function extractRobotsDirectives(\DOMXPath $xpath): Robots
    {
        $nodes = $xpath->query('//meta[@name]');
        if ($nodes === false) {
            return new Robots(true, true);
        }

        $index = true;
        $follow = true;

        foreach ($nodes as $meta) {
            $name = strtolower(trim((string) $meta->attributes?->getNamedItem('name')?->nodeValue));
            if ($name !== 'robots') {
                continue;
            }

            $content = strtolower((string) $meta->attributes?->getNamedItem('content')?->nodeValue);
            $index = !str_contains($content, 'noindex');
            $follow = !str_contains($content, 'nofollow');
            break;
        }

        return new Robots($index, $follow);
    }

    private function extractCanonicalUrl(\DOMXPath $xpath, string $baseUrl): string
    {
        $nodes = $xpath->query('//link[@rel]');
        if ($nodes === false) {
            return $baseUrl;
        }

        foreach ($nodes as $link) {
            $rel = strtolower(trim((string) $link->attributes?->getNamedItem('rel')?->nodeValue));
            if ($rel !== 'canonical') {
                continue;
            }

            $href = trim((string) $link->attributes?->getNamedItem('href')?->nodeValue);
            if ($href === '') {
                break;
            }

            return $this->resolve($baseUrl, $href) ?? $baseUrl;
        }

        return $baseUrl;
    }

    /**
     * @return list<string>
     */
    private function extractLinks(\DOMXPath $xpath, string $baseUrl): array
    {
        $result = [];

        $nodes = $xpath->query('//a[@href]');
        if ($nodes === false) {
            return [];
        }

        foreach ($nodes as $anchor) {
            $href = trim((string) $anchor->attributes?->getNamedItem('href')?->nodeValue);
            if ($href === '') {
                continue;
            }

            $resolved = $this->resolve($baseUrl, $href);
            if ($resolved === null) {
                continue;
            }

            $result[$resolved] = true;
        }

        return array_keys($result);
    }

    private function resolve(string $base, string $url): ?string
    {
        try {
            $resolved = Uri::new($base)->resolve($url);

            $resolved = $resolved->withFragment(null);

            if (! in_array($resolved->getScheme(), ['http', 'https'], true)) {
                return null;
            }

            return (string) $resolved;
        } catch (\Throwable) {
            return null;
        }
    }
}