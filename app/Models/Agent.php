<?php

namespace App\Models;

use App\Enums\ChatOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string name
 * @property string prompt
 * @property array chatOptions
 * @property int id_user
 */
class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'prompt',
        'chatOptions',
        'id_user'
    ];

    protected $casts = [
        'chatOptions' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'id_agent');
    }

    public function getOption(ChatOptions $option): mixed
    {
        return $this->chatOptions[$option->value] ?? null;
    }
}
