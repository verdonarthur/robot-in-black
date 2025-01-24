<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', static function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->string('title');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_agent');
            $table->timestamps();
        });
    }
};
