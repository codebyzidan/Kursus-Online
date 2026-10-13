<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('headline')->nullable()->after('phone');
            $table->text('bio')->nullable()->after('headline');
            $table->timestamp('last_seen_at')->nullable()->after('email_verified_at');
            $table->boolean('is_active')->default(true)->after('last_seen_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'headline', 'bio', 'last_seen_at', 'is_active']);
        });
    }
};
