<?php

namespace App\Filament\Console\Resources\AgentResource\Pages;

use App\Filament\Console\Resources\AgentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAgent extends CreateRecord
{
    protected static string $resource = AgentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if(data_get($data, 'chatPassword', null)) {
            $data['chatOptions']['password'] = $data['chatPassword'];
        }

        if(data_get($data, 'searchPlaceholder', null)) {
            $data['chatOptions']['searchPlaceholder'] = $data['searchPlaceholder'];
        }

        $data['id_user'] = auth()->id();
        return $data;
    }
}
