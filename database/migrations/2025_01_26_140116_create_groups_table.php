<?php

use App\Enums\UserGroupEnum;
use App\Models\User\Group;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('groups', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('users', static function (Blueprint $table) {
            $table
                ->foreignId('group_id')
                ->after('remember_token')
                ->nullable()
                ->constrained()
                ->noActionOnDelete()
                ->noActionOnUpdate();
        });

        Group::query()->insert([
            ['name' => UserGroupEnum::USER, 'created_at' => now()],
            ['name' => UserGroupEnum::ADMIN, 'created_at' => now()],
        ]);
    }
};
