<?php

namespace Tonsoo\SitemapGenerator\Data;

final readonly class Robots
{
    public function __construct(
        public bool $index = true,
        public bool $follow = true,
    ) { }
}
