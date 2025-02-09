<?php

namespace App\Services\AI;

use App\Exceptions\EmbeddingException;
use App\Exceptions\PromptException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use JsonException;

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
     * @throws EmbeddingException
     * @throws ConnectionException
     * @throws JsonException
     */
    public function getEmbedding(string $content): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->embeddingUrl, [
            'input' => $content,
            'model' => $this->getEmbeddingModelName(),
        ])->json();

        if (isset($response['error']) || (data_get($response, 'result.error', false))) {
            throw new EmbeddingException(json_encode($response['error'], JSON_THROW_ON_ERROR));
        }

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
     * @throws PromptException
     * @throws ConnectionException
     * @throws JsonException
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
        ])->post($this->textCompletionUrl, [
            'model' => self::MODEL_NAME,
            'messages' => $messages,
            'temperature' => 1.0,
            'max_tokens' => 800,
        ])->json();

        if (isset($response['error']) || (data_get($response, 'result.error', false))) {
            throw new PromptException(json_encode($response['error'], JSON_THROW_ON_ERROR));
        }

        return data_get($response, 'choices.0.message.content', '');
    }

    public function getMaximumPromptTokenLength(): int
    {
        return 99_000;
    }
}
