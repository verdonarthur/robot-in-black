<?php

namespace App\Models;

use App\Providers\EmbeddingServiceProvider;
use App\Services\AI\EmbeddingServiceInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JsonException;

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

    /**
     * @throws JsonException
     * @return Collection<Document>
     */
    public static function orderedByContentDistance(string $content, Agent $agent): Collection
    {
        /**
         * @type EmbeddingServiceInterface $embeddingService
         */
        $embeddingService = app(EmbeddingServiceProvider::EMBEDDING_SERVICE);
        $maxDocument = 1;

        $embedding = $embeddingService->getEmbedding($content);

        $agentDocumentIds = $agent->documents()->pluck('id');

        $documentIds = DocumentEmbedding::query()
            ->whereIn('document_id', $agentDocumentIds)
            ->orderByRaw("VEC_DISTANCE_EUCLIDEAN(VEC_FROMTEXT(?), embedding_{$embeddingService->getNbrOfVector()})", [json_encode($embedding, JSON_THROW_ON_ERROR)])
            ->limit($maxDocument)
            ->get()
            ->pluck('document_id')
            ->unique();

        return self::query()->findMany($documentIds);
    }

    protected static function booted(): void
    {
        $embeddingService = app(EmbeddingServiceProvider::EMBEDDING_SERVICE);

        static::saved(static function (Document $document) use ($embeddingService) {
            if (! $document->isDirty('content')) {
                return;
            }

            $document->embeddings()->delete();

            $embeddings = Str::of($document->content)
                ->replaceMatches('/[^a-zA-Z0-9\s]/', '')
                ->split('/\s+/')
                ->chunk(2040)
                ->flatMap(
                    fn(Collection $splitContentChunk) => Str::of($splitContentChunk
                        ->implode(' '))
                        ->split(9_000),
                )
                ->map(static function (string $text) use ($embeddingService) {
                    return $embeddingService
                        ->getEmbedding($text);
                })
                ->map(static function ($embedding) use ($embeddingService) {
                    $nbrOfVector = $embeddingService->getNbrOfVector();
                    return new DocumentEmbedding([
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
