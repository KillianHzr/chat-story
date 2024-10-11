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
        $response = $this->client->request('POST', 'https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => array_merge([
                'model' => 'gpt-4o-2024-08-06',
                'messages' => [
                    ['role' => 'user', 'content' => 'Are semicolons optional in JavaScript?'],
                    ['role' => 'system', 'content' => "Tu es un assistant qui permet d'écrire un histoire à choix multiples. Ton rôle est narrateur, ton but est de rédiger des parties d'histoires puis de proposer des choix qui guideront la suite de l'histoire. 
                    L'histoire doit être cohérente et suivre une chronologie logique. Le thème doit être le même. A la fin de chaque partie, tu dois proposer 3 choix. 
                    Renvoie un string au format JSON, avec un champ 'choices' (tableau de string) un champ 'story' (string) "]
                ],
                'temperature' => 0.7,
                'response_format' => [
                    'type' => 'json_schema',
                    'json_schema' => [
                        'name' => 'sdfs',
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'choices'=> [
                                    'type' => 'array',
                                    'maxItems' => 3,
                                    'items' => [
                                        'type' => 'string',
                                    ]
                                ],
                                'story' => [
                                    'type' => 'string'
                                ]
                            ]
                         ]
                    ]
                ]
            ], $options)
        ]);

        try {
            $result = $response->toArray();
            $content = (string) $result['choices'][0]['message']['content'];
            dump($content);
            return [];
        }
        catch (\Exception $e) {
            dump($e);
            return [];
        }
    }
}
