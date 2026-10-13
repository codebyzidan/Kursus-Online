<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('course_title');
            $table->unsignedInteger('price');
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('instructor_earning')->default(0);
            $table->timestamps();

            $table->unique(['order_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
