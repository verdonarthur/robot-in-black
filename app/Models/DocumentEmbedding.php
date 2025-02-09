<?php

namespace App\Models;

use App\Casts\Vector;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentEmbedding extends Model
{
    use HasFactory;

    protected $fillable = [
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
}
