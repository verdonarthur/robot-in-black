<?php

namespace App\Services\AI;

interface EmbeddingServiceInterface
{
    public function getEmbedding(string $content): array;

    public function getEmbeddingModelName(): string;

    public function getNbrOfVector(): int;
}
