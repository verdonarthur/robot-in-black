<?php

namespace App\Providers;

use App\Services\AI\GeminiService;
use App\Services\AI\InfomaniakAiService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class EmbeddingServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public const EMBEDDING_SERVICE = 'embedding_service';

    public function register(): void
    {
        $this->app->singleton(self::EMBEDDING_SERVICE, function ($app) {
            if (config('ai.embedding_service') === 'infomaniak') {
                return new InfomaniakAiService();
            }

            return new GeminiService();
        });
    }

    public function provides(): array
    {
        return [self::EMBEDDING_SERVICE];
    }
}
