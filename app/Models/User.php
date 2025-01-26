<?php

namespace App\Models;

use App\Enums\UserGroupEnum;
use App\Models\User\Group;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\ConsolePanelProvider;
use Database\Factories\UserFactory;
use Exception;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'password',
        'group_id',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @throws Exception
     */
    public function canAccessPanel(Panel $panel): bool
    {
        $panelId = $panel->getId();

        if ($panelId === AdminPanelProvider::ID && $this->group->name === UserGroupEnum::ADMIN) {
            return true;
        }

        if ($panelId === ConsolePanelProvider::ID
            && in_array($this->group->name, [UserGroupEnum::ADMIN, UserGroupEnum::USER], true)) {
            return true;
        }

        return false;
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
