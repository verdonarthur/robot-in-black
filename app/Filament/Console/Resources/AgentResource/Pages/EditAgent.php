<?php

namespace App\Filament\Console\Resources\AgentResource\Pages;

use App\Filament\Console\Resources\AgentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgent extends EditRecord
{
    protected static string $resource = AgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if($data['chatPassword'] ?? null) {
            $data['chatOptions']['password'] = $data['chatPassword'];
        }

        if($data['searchPlaceholder'] ?? null) {
            $data['chatOptions']['searchPlaceholder'] = $data['searchPlaceholder'];
        }

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if(data_get($data, 'chatOptions.password')) {
            $data['isPasswordProtected'] = true;
            $data['chatPassword'] = data_get($data, 'chatOptions.password');
        }

        if(data_get($data, 'chatOptions.searchPlaceholder')) {
            $data['searchPlaceholder'] = data_get($data, 'chatOptions.searchPlaceholder');
        }

        return $data;
    }
}
