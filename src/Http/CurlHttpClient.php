<?php

namespace Tonsoo\PhpCrawler\Http;

use CurlHandle;
use Tonsoo\PhpCrawler\Data\Result;

class CurlHttpClient implements HttpClientInterface
{
    private CurlHandle $curl;

    public function __construct()
    {
        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($this->curl, CURLOPT_VERBOSE, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 4);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 4);
        curl_setopt($this->curl, CURLOPT_MAXREDIRS, 10);

        curl_setopt($this->curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64; rv:60.0) Gecko/20100101 Firefox/81.0');
    }

    public function fetch(string $url): Result
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);

        $html = curl_exec($this->curl);
        $effectiveUrl = (string) curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
        $statusCode = (int) curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $contentType = (string) (curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE) ?? '');
        $responseTimeMs = (float) curl_getinfo($this->curl, CURLINFO_TOTAL_TIME) * 1000;

        return new Result(
            requestedUrl: $url,
            effectiveUrl: $effectiveUrl !== '' ? $effectiveUrl : $url,
            html: is_string($html) ? $html : null,
            statusCode: $statusCode,
            contentType: $contentType,
            responseTimeMs: $responseTimeMs,
        );
    }
}