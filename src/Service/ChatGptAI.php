<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ChatGptAI
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function requestChatGPT(string $prompt, array $options = []): array
    {
        $response = $this->client->request('POST', 'https://api.openai.com/v1/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => array_merge([
                'model' => 'text-davinci-003',
                'prompt' => $prompt,
                'max_tokens' => 1000,
                'temperature' => 0.7,
            ], $options)
        ]);

        return $response->toArray();
    }
}
