<?php

namespace App\Services\AI;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class InfomaniakAiService implements EmbeddingServiceInterface, PromptServiceInterface
{
    public const MODEL_NAME = 'granite';

    private string $productId;
    private string $apiKey;
    private string $embeddingUrl;
    private string $textCompletionUrl;

    public function __construct()
    {
        $this->productId = config('ai.infomaniak.product_id', '');
        $this->apiKey = config('ai.infomaniak.key', '');

        $setProductIdInURL = fn(string $url) => Str::of($url)->replace('{product_id}', $this->productId);
        $this->embeddingUrl = $setProductIdInURL('https://api.infomaniak.com/1/ai/{product_id}/openai/v1/embeddings');
        $this->textCompletionUrl = $setProductIdInURL('https://api.infomaniak.com/1/ai/{product_id}/openai/chat/completions');
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function getEmbedding(string $content): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])
            ->retry(3, 60_000)
            ->post($this->embeddingUrl, [
                'input' => $content,
                'model' => $this->getEmbeddingModelName(),
            ])
            ->throwIfStatus(429)
            ->throwIf(function (Response $response) {
                return $response->json('result.error', false);
            })
            ->json();

        return data_get($response, 'data.0.embedding', []);
    }

    public function getEmbeddingModelName(): string
    {
        return 'mini_lm_l12_v2';
    }

    public function getNbrOfVector(): int
    {
        return 384;
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function prompt(string $prompt, string $systemPrompt): string
    {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $prompt],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])
            ->retry(3, 5000)
            ->post($this->textCompletionUrl, [
                'model' => self::MODEL_NAME,
                'messages' => $messages,
                'temperature' => 1.0,
                'max_tokens' => 800,
            ])
            ->throwIf(function (Response $response) {
                return $response->json('result.error', false);
            })
            ->json();

        return data_get($response, 'choices.0.message.content', '');
    }

    public function getMaximumPromptTokenLength(): int
    {
        return 99_000;
    }
}
