<?php

use Tonsoo\SitemapGenerator\Crawler\Crawler;

if (! function_exists('crawler')) {
    function crawler(): Crawler
    {
        return new Crawler();
    }
}