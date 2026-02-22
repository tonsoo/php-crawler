<?php

namespace Tonsoo\PhpCrawler\Http;

use Tonsoo\PhpCrawler\Data\Result;

interface HttpClientInterface
{
    public function fetch(string $url): Result;
}