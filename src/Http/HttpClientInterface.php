<?php

namespace Tonsoo\SitemapGenerator\Http;

use Tonsoo\SitemapGenerator\Data\Result;

interface HttpClientInterface
{
    public function fetch(string $url): Result;
}