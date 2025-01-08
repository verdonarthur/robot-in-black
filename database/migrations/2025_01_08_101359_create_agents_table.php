<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('prompt');
            $table->json('chatOptions')->nullable();
            $table->unsignedBigInteger('id_user');
            $table->timestamps();
        });
    }
};
