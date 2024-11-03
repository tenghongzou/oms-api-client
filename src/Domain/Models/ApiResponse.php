<?php

namespace LyraRingNet\OmsApiClient\Domain\Models;

class ApiResponse
{
    public $status;
    public $url;
    public $headers;
    public $body;
    public $timestamp;

    public function __construct(array $data)
    {
        $this->status = $data['status'] ?? null;
        $this->url = $data['url'];
        $this->headers = $data['headers'];
        $this->body = $data['body'];
        $this->timestamp = $data['timestamp'];
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'status' => $this->status,
            'headers' => $this->headers,
            'body' => $this->body,
            'timestamp' => $this->timestamp,
        ];
    }
}
