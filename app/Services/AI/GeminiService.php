<?php

namespace App\Services\AI;

use App\Exceptions\EmbeddingException;
use App\Exceptions\PromptException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use JsonException;

class GeminiService implements EmbeddingServiceInterface, PromptServiceInterface
{
    public const MODEL_NAME = 'gemini-2.0-flash';

    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        $this->apiKey = config('ai.gemini.key');
    }

    protected function getApiUrl(string $modelName, string $type): string
    {
        return "{$this->baseUrl}/models/{$modelName}:{$type}?key={$this->apiKey}";
    }

    /**
     * @throws ConnectionException
     * @throws JsonException
     * @throws EmbeddingException
     */
    public function getEmbedding(string $content): array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->getApiUrl($this->getEmbeddingModelName(), 'embedContent'), [
            'model' => "models/{$this->getEmbeddingModelName()}",
            'content' => [
                'parts' => [
                    ['text' => $content],
                ],
            ],
        ])->json();

        if (isset($response['error'])) {
            throw new EmbeddingException(json_encode($response['error'], JSON_THROW_ON_ERROR));
        }

        return data_get($response, 'embedding.values');
    }

    /**
     * @throws PromptException
     * @throws ConnectionException
     * @throws JsonException
     */
    public function prompt(string $prompt, string $systemPrompt): string
    {
        $modelName = self::MODEL_NAME;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->getApiUrl($modelName, 'generateContent'), [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemPrompt],
                ],
            ],
            'contents' => [
                'parts' => [
                    ['text' => $prompt],
                ],
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_ONLY_HIGH',
                ],
            ],
            'generationConfig' => [
                'temperature' => 1.0,
                'maxOutputTokens' => 800,
            ],
        ])->json();

        if (isset($response['error'])) {
            throw new PromptException(json_encode($response['error'], JSON_THROW_ON_ERROR));
        }

        return data_get($response, 'candidates.0.content.parts.0.text', '');
    }

    public function getEmbeddingModelName(): string
    {
        return 'text-embedding-004';
    }

    public function getNbrOfVector(): int
    {
        return 768;
    }

    public function getMaximumPromptTokenLength(): int
    {
        return 99_000;
    }
}
