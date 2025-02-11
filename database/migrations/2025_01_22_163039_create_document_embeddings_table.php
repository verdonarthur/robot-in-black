<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_embeddings',
            static function (Blueprint $table) {
                $table->id();
                $table->text('content');
                $table->vector('embedding_384', 384)->nullable();
                $table->vector('embedding_768', 768)->nullable();
                $table->vector('embedding_3584', 3584)->nullable();
                $table->unsignedBigInteger('document_id');
                $table->foreign('document_id')
                    ->references('id')
                    ->on('documents')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
                $table->timestamps();
            });
    }
};
