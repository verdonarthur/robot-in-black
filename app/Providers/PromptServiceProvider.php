<?php

namespace App\Providers;

use App\Services\AI\GeminiService;
use App\Services\AI\GroqService;
use App\Services\AI\InfomaniakAiService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class PromptServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public const AVAILABLE_PROMPT_SERVICES = [
        'gemini-flash-2' => [
            'class' => GeminiService::class,
            'config' => 'ai.gemini.key',
            'displayName' => 'Gemini Flash 2.0',
        ],
        'groq-deepseek' => [
            'class' => GroqService::class,
            'config' => 'ai.groq.key',
            'displayName' => 'Groq DeepSeek R1 (LLama Distilled) 70b',
        ],
        'infomaniak-granite' => [
            'class' => InfomaniakAiService::class,
            'config' => 'ai.infomaniak.key',
            'displayName' => 'Infomaniak Granite 3.1 8b',
        ],
    ];

    public function register(): void
    {
        foreach (self::AVAILABLE_PROMPT_SERVICES as $service) {
            $this->app->singleton($service['class'], function () use ($service) {
                return new $service['class']();
            });
        }
    }

    public static function getActivatedServicesAsOptions(): array
    {
        return collect(self::AVAILABLE_PROMPT_SERVICES)->mapWithKeys(function ($config, $name) {
            if (! config($config['config'])) {
                return false;
            }

            return [
                $name => $config['displayName'],
            ];
        })->filter()->toArray();
    }

    public static function getServiceClassFromName(string $serviceName): ?string
    {
        return data_get(self::AVAILABLE_PROMPT_SERVICES, $serviceName . '.class');
    }

    public function provides(): array
    {
        return collect(self::AVAILABLE_PROMPT_SERVICES)->map(
            fn($service) => $service['class'],
        )->values()->toArray();
    }
}
