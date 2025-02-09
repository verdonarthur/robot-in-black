<?php

namespace App\Filament\Console\Resources\AgentResource\Pages;

use App\Enums\ChatOptions;
use Illuminate\Support\Facades\Hash;

trait HasChatOptions
{
    public function setChatOptions(array $data): array
    {
        foreach (ChatOptions::cases() as $case) {
            if (! isset($data[$case->value])) {
                continue;
            }

            $data['chatOptions'][$case->value] = match ($case) {
                ChatOptions::PASSWORD_HASH => Hash::make($data[$case->value]),
                default => $data[$case->value],
            };

            unset($data[$case->value]);
        }

        return $data;
    }
}
