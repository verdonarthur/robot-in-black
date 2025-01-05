<?php

namespace App\Providers\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiProvider
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

            return $response['embedding']['values'] ?? [];
        } catch (\Exception $e) {
            Log::error('Gemini Embedding Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getEmbeddings(array $contents): array
    {
        $embeddings = [];
        foreach ($contents as $content) {
            $embeddings[] = $this->getEmbedding($content);
        }
        return $embeddings;
    }

    public function prompt(string $prompt): string
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post("{$this->baseUrl}/models/gemini-1.5-flash:generateContent?key={$this->apiKey}", [
                'contents' => [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ],
//                'safetySettings' => [
//                    [
//                        'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
//                        'threshold' => 'BLOCK_ONLY_HIGH'
//                    ]
//                ],
                'generationConfig' => [
//                    'stopSequences' => ['Title'],
                    'temperature' => 1.0,
                    'maxOutputTokens' => 800,
//                    'topP' => 0.8,
//                    'topK' => 10
                ]
            ])->json();

            return $response['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
        } catch (\Exception $e) {
            Log::error('Gemini Prompt Error: ' . $e->getMessage());
            return 'An error occurred while processing the prompt.';
        }
    }
}
