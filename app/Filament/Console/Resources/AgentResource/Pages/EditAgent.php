<?php

namespace App\Filament\Console\Resources\AgentResource\Pages;

use App\Enums\ChatOptions;
use App\Filament\Console\Resources\AgentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgent extends EditRecord
{
    use HasChatOptions;

    protected static string $resource = AgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->setChatOptions($data);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach (ChatOptions::cases() as $case) {
            if (! $value = data_get($data, "chatOptions.{$case->value}")) {
                continue;
            }

            if ($case === ChatOptions::PASSWORD_HASH) {
                $data['isPasswordProtected'] = true;
                continue;
            }

            $data[$case->value] = $value;
        }

        return $data;
    }
}
