<?php

namespace App\Models\User;

use App\Enums\UserGroupEnum;
use App\Models\User;
use Database\Factories\User\GroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    /** @use HasFactory<GroupFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'name' => UserGroupEnum::class,
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
