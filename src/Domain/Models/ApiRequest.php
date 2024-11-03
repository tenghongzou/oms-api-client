<?php

namespace LyraRingNet\OmsApiClient\Domain\Models;

class ApiRequest
{
    public $method;
    public $url;
    public $headers;
    public $body;
    public $timestamp;

    public function __construct(array $data)
    {
        $this->method = $data['method'];
        $this->url = $data['url'];
        $this->headers = $data['headers'];
        $this->body = $data['body'];
        $this->timestamp = $data['timestamp'];
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'method' => $this->method,
            'headers' => $this->headers,
            'body' => $this->body,
            'timestamp' => $this->timestamp,
        ];
    }
}
