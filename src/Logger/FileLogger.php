<?php

namespace Tonsoo\SitemapGenerator\Logger;

class FileLogger implements LoggerInterface
{
    use LoggerTrait;

    public function __construct(
        private readonly string $path = 'crawl.log',
    ) {}

    public function log(string $message): void
    {
        $message = $this->addTimestamps($message);

        $file = fopen($this->path, 'a');
        if ($file) {
            fwrite($file, $message . PHP_EOL);
            fclose($file);
        }
    }
}