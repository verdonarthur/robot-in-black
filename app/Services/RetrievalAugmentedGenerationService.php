<?php

namespace App\Services;

use App\Services\AI\GeminiService;
use Illuminate\Support\Str;

class RetrievalAugmentedGenerationService
{
    public const REQUEST_TEMPLATE_VAR = '%REQUEST%';
    public const CONTEXT_TEMPLATE_VAR = '%CONTEXT%';

    protected GeminiService $gemini;

    public function __construct()
    {
        $this->gemini = app(GeminiService::class);
    }

    public static function getSystemPrompt(): string
    {
        return <<<SYSTEM_PROMPT
            As a an AI assisted with Retrieval Augmented Generation,
            you will always have a prompt with a question and some document content as a context to answer the question.

            You should never invent your answer. Always base it on the document content.

            You should always give an answer formated in markdown format.

            Always try to answer in the language of the question if possible. Otherwise, do use english.
        SYSTEM_PROMPT;
    }

    public function generateAnswer(string $userPrompt, string $agentPrompt, string $content): string
    {
        $finalPrompt = Str::of($agentPrompt)
            ->replace(self::REQUEST_TEMPLATE_VAR, $userPrompt)
            ->replace(self::CONTEXT_TEMPLATE_VAR, $content);

        return $this->gemini->prompt($finalPrompt, self::getSystemPrompt());
    }
}
