<?php

namespace App\Models;

use App\Providers\EmbeddingServiceProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $content
 * @property ?int $id_user
 * @property ?int $id_agent
 */
class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'title',
        'id_user',
        'id_agent',
    ];

    protected $casts = [
        'content' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'id_agent');
    }

    protected static function booted(): void
    {
        $embeddingService = app(EmbeddingServiceProvider::EMBEDDING_SERVICE);

        static::saved(static function (Document $document) use ($embeddingService) {
            if (! $document->isDirty('content')) {
                return;
            }

            $document->embeddings()->delete();

            $microSecondsToPreventRateLimit = 200;
            $embeddings = Str::of($document->content)
                ->explode(' ')
                ->chunk(DocumentEmbedding::MAX_TOKEN)
                ->map(static function (Collection $chunk) use ($embeddingService, $microSecondsToPreventRateLimit) {
                    $text = $chunk->implode(' ');
                    $embedding = $embeddingService->getEmbedding($text);
                    $nbrOfVector = $embeddingService->getNbrOfVector();

                    usleep($microSecondsToPreventRateLimit);

                    return new DocumentEmbedding([
                        'content' => $text,
                        "embedding_{$nbrOfVector}" => $embedding,
                    ]);
                });

            $document->embeddings()->saveMany($embeddings);
        });
    }

    public function embeddings(): HasMany
    {
        return $this->hasMany(DocumentEmbedding::class);
    }
}
