<?php

namespace App\Models;

use App\Casts\Vector;
use App\Providers\AI\GeminiProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JsonException;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'title',
        'embedding',
        'id_user',
        'id_agent'
    ];

    protected $casts = [
        'embedding' => Vector::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'id_agent');
    }

    /**
     * @throws JsonException
     */
    public static function orderedByContentDistance(string $content)
    {
        $embedding = app(GeminiProvider::class)->getEmbedding($content);

        return self::query()->orderByRaw('VEC_DISTANCE_EUCLIDEAN(VEC_FROMTEXT(?), embedding)', [json_encode($embedding, JSON_THROW_ON_ERROR)])->get();
    }

    protected static function booted(): void
    {
        static::saving(static function ($document) {
            if ($document->isDirty('content')) {
                $document->content = Str::of($document->content)
                    ->explode(' ')
                    ->take(2000)
                    ->implode(' ');

                $document->embedding = app(GeminiProvider::class)
                    ->getEmbedding($document->content);
            }
        });
    }
}
