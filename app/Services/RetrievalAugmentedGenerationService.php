<?php

namespace App\Services;

use App\Enums\ChatOptions;
use App\Exceptions\PromptException;
use App\Models\Agent;
use App\Providers\PromptServiceProvider;
use App\Services\AI\PromptServiceInterface;
use Illuminate\Support\Str;

class RetrievalAugmentedGenerationService
{
    public const REQUEST_TEMPLATE_VAR = '%REQUEST%';
    public const CONTEXT_TEMPLATE_VAR = '%CONTEXT%';

    public function __construct(protected TokenizerService $tokenizerService)
    {
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

    protected function limitContentSize(string $content, int $maxToken): string
    {
        $maxNumberOfCharacter = $this->tokenizerService->tokenize($content)->take(
            $maxToken,
        )->reduce(fn(int $acc, string $token) => strlen($token) + $acc, 0);

        return Str::of($content)->take($maxNumberOfCharacter);
    }

    /**
     * @throws PromptException
     */
    public function generateAnswer(string $userPrompt, Agent $agent, string $content): string
    {
        $promptServiceClass = PromptServiceProvider::getServiceClassFromName(
            $agent->getOption(ChatOptions::PROMPT_MODEL),
        );
        /** @type ?PromptServiceInterface $promptService */
        $promptService = app($promptServiceClass);
        if (! $promptServiceClass || ! $promptService) {
            return '';
        }

        $finalPrompt = Str::of($agent->prompt)
            ->replace(self::REQUEST_TEMPLATE_VAR, $userPrompt);

        $finalPrompt = $finalPrompt
            ->replace(
                self::CONTEXT_TEMPLATE_VAR,
                $this->limitContentSize(
                    $content,
                    $promptService->getMaximumPromptTokenLength() - $this->tokenizerService->tokenize($finalPrompt)->count() - 1000,
                ),
            );

        return $promptService->prompt($finalPrompt, self::getSystemPrompt());
    }
}
