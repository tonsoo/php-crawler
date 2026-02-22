<?php

namespace Tonsoo\PhpCrawler\Analysis;

use Tonsoo\PhpCrawler\Data\Result;

interface PageAnalyzerInterface
{
    public function analyze(Result $result, bool $respectNoIndex = true, bool $respectNoFollow = true): PageAnalysis;
}