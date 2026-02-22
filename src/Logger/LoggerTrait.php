<?php

namespace Tonsoo\PhpCrawler\Logger;

use Carbon\Carbon;

trait LoggerTrait
{
    protected function addTimestamps(string $message): string
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        return "[$now] {$message}";
    }
}