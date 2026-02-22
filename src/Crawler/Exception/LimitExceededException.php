<?php

namespace Tonsoo\SitemapGenerator\Crawler\Exception;

use Tonsoo\SitemapGenerator\Exception\CrawlerException;

class LimitExceededException extends CrawlerException
{
    public static function pages(int $limit): self
    {
        return new self(
            sprintf('The maximum allowed number of pages (%d) has been exceeded.', $limit),
            $limit
        );
    }
}