<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('subtotal')->default(0);
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->string('status')->default('pending');
            $table->string('payment_channel')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('snap_token')->nullable();
            $table->string('gateway_ref')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('gateway_ref');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
