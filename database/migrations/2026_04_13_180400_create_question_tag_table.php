<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Промежуточная таблица между вопросами и тегами.
     */
    public function up(): void
    {
        Schema::create('question_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['question_id', 'tag_id']);
        });
    }

    /**
     * Удаляем промежуточную таблицу.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_tag');
    }
};
