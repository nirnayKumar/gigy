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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('earnings_entry_id')->nullable()->constrained('earnings_entries')->nullOnDelete();
            $table->string('company_name');
            $table->string('issue_category');
            $table->decimal('expected_amount', 10, 2)->nullable();
            $table->decimal('received_amount', 10, 2)->nullable();
            $table->date('incident_date')->nullable();
            $table->text('description');
            $table->string('preferred_language')->default('English');
            $table->longText('generated_draft')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
