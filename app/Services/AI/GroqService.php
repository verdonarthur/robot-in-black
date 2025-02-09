<?php

namespace App\Services\AI;

use App\Exceptions\PromptException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use JsonException;

class GroqService implements PromptServiceInterface
{
    public const MODEL_NAME = 'deepseek-r1-distill-llama-70b';
    private string $apiKey;
    private string $baseUrl = 'https://api.groq.com/openai/v1';

    public function __construct()
    {
        $this->apiKey = config('ai.groq.key', '');
    }

    /**
     * @throws ConnectionException
     * @throws JsonException
     * @throws PromptException
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
        ])->post("{$this->baseUrl}/chat/completions", [
            'messages' => $messages,
            'model' => self::MODEL_NAME,
            'temperature' => 1.0,
            'max_tokens' => 800,
        ])->json();

        if (isset($response['error'])) {
            throw new PromptException(json_encode($response['error'], JSON_THROW_ON_ERROR));
        }

        $promptResult = data_get($response, 'choices.0.message.content', '');
        return Str::of($promptResult)->replace('/<think>.*?<\/think>/', '');
    }

    public function getMaximumPromptTokenLength(): int
    {
        return 6000;
    }
}
