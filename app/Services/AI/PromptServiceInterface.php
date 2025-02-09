<?php

namespace App\Services\AI;

use App\Exceptions\PromptException;

interface PromptServiceInterface
{
    /**
     * @throws PromptException
     */
    public function prompt(string $prompt, string $systemPrompt): string;

    public function getMaximumPromptTokenLength(): int;
}
