<?php

namespace App\Services\AI;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GeminiService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    public function getEmbedding(string $content): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post("{$this->baseUrl}/models/text-embedding-004:embedContent?key={$this->apiKey}", [
                'model' => 'models/text-embedding-004',
                'content' => [
                    'parts' => [
                        ['text' => $content]
                    ]
                ]
            ])->json();

            if(isset($response['error'])) {
                throw new RuntimeException(json_encode($response['error']));
            }

            return $response['embedding']['values'] ?? [];
        } catch (Exception $e) {
            Log::error('Gemini Embedding Error: ' . $e->getMessage());
            return [];
        }
    }

    public function prompt(string $prompt, string $systemPrompt): string
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post("{$this->baseUrl}/models/gemini-1.5-flash:generateContent?key={$this->apiKey}", [
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $systemPrompt],
                    ],
                ],
                'contents' => [
                    'parts' => [
                        ['text' => $prompt]
                    ]
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
                ]
            ])->json();

            if (isset($response['error'])) {
                throw new RuntimeException(json_encode($response['error']));
            }

            return $response['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
        } catch (Exception $e) {
            Log::error('Gemini Prompt Error: ' . $e->getMessage());
            return 'An error occurred while processing the prompt.';
        }
    }
}
