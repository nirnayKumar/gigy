<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'worker_type')) {
                $table->string('worker_type')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'preferred_language')) {
                $table->string('preferred_language')->default('English')->after('worker_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['worker_type', 'preferred_language']);
        });
    }
};
