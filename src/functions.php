<?php

use Tonsoo\PhpCrawler\Crawler\Crawler;

if (! function_exists('crawler')) {
    function crawler(): Crawler
    {
        return new Crawler();
    }
}