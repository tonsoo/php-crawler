<?php

namespace Tonsoo\SitemapGenerator\Logger;

class ConsoleLogger implements LoggerInterface
{
    use LoggerTrait;

    public function log(string $message): void
    {
        $message = $this->addTimestamps($message) . PHP_EOL;
        echo $message;
    }
}