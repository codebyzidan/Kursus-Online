<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('lesson_questions')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->index(['lesson_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_questions');
    }
};
