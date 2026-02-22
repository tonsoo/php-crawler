<?php

namespace Tonsoo\PhpCrawler\Data;

final readonly class Result
{
    public function __construct(
        public string  $requestedUrl,
        public string  $effectiveUrl,
        public ?string $html,
        public int     $statusCode,
        public string  $contentType,
        public float   $responseTimeMs,
    ) {
    }

    public function hasHtmlBody(): bool
    {
        return $this->html !== null && $this->html !== '';
    }

    public function isHtmlContentType(): bool
    {
        return str_contains(strtolower($this->contentType), 'text/html');
    }
}
