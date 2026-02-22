<?php

namespace Tonsoo\SitemapGenerator\Analysis;

use Tonsoo\SitemapGenerator\Data\Result;

interface PageAnalyzerInterface
{
    public function analyze(Result $result, bool $respectNoIndex = true, bool $respectNoFollow = true): PageAnalysis;
}