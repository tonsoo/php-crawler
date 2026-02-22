<?php

namespace Tonsoo\PhpCrawler\Data;

final readonly class Robots
{
    public function __construct(
        public bool $index = true,
        public bool $follow = true,
    ) { }
}
