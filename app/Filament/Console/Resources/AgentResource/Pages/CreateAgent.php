<?php

namespace App\Filament\Console\Resources\AgentResource\Pages;

use App\Filament\Console\Resources\AgentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAgent extends CreateRecord
{
    use HasChatOptions;

    protected static string $resource = AgentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id_user'] = auth()->id();
        return $this->setChatOptions($data);
    }
}
