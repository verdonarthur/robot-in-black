<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Enums\UserGroupEnum;
use App\Filament\Admin\Resources\UserResource;
use App\Models\User\Group;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['group_id'] = Group::query()->whereName(UserGroupEnum::USER->value)->firstOrFail()->id;

        return $data;
    }
}
