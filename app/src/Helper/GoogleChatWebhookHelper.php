<?php

namespace App\Service;

class GoogleChatWebhookHelper
{
    private string $url;
    private string $spaceId;
    private string $key;
    private string $token;

    public function __construct(
        string $url,
        string $spaceId,
        string $key,
        string $token
    ) {
        $this->url = $url;
        $this->spaceId = $spaceId;
        $this->key = $key;
        $this->token = $token;
    }

    public function getWebhookUrl(): string
    {
        return sprintf(
            '%s/%s/messages?key=%s&token=%s',
            $this->url,
            $this->spaceId,
            $this->key,
            $this->token
        );
    }
}
// This class is responsible for generating the Google Chat webhook URL.