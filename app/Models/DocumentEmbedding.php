<?php

namespace App\Models;

use App\Casts\Vector;
use App\Providers\EmbeddingServiceProvider;
use App\Services\AI\EmbeddingServiceInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use JsonException;

/**
 * @property int $id
 * @property string $content
 * @property ?array $embedding_384
 * @property ?array $embedding_768
 * @property ?array embedding_3584
 * @property int id_document
 */
class DocumentEmbedding extends Model
{
    use HasFactory;

    public const MAX_TOKEN = 512;

    protected $fillable = [
        'content',
        'embedding_384',
        'embedding_768',
        'embedding_3584',
        'id_document',
    ];

    protected $casts = [
        'embedding_384' => Vector::class,
        'embedding_768' => Vector::class,
        'embedding_3584' => Vector::class,
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }


    /**
     * @return Collection<Document>
     * @throws JsonException
     */
    public static function orderedByContentDistance(string $content, Agent $agent): Collection
    {
        /**
         * @type EmbeddingServiceInterface $embeddingService
         */
        $embeddingService = app(EmbeddingServiceProvider::EMBEDDING_SERVICE);
        $maxDocument = 3;

        $embedding = $embeddingService->getEmbedding($content);

        $agentDocumentIds = $agent->documents()->pluck('id');

        return self::query()
            ->whereIn('document_id', $agentDocumentIds)
            ->orderByRaw("VEC_DISTANCE_EUCLIDEAN(VEC_FROMTEXT(?), embedding_{$embeddingService->getNbrOfVector()})", [json_encode($embedding, JSON_THROW_ON_ERROR)])
            ->limit($maxDocument)
            ->get();
    }
}
