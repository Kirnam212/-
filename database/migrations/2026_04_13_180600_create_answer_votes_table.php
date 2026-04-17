<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Таблица голосов за ответы.
     */
    public function up(): void
    {
        Schema::create('answer_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->smallInteger('value');
            $table->timestamps();
            $table->unique(['answer_id', 'user_id']);
        });
    }

    /**
     * Удаляем таблицу голосов за ответы.
     */
    public function down(): void
    {
        Schema::dropIfExists('answer_votes');
    }
};
