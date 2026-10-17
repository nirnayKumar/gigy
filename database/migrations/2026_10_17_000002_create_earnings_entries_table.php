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
        Schema::create('earnings_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('work_date');
            $table->decimal('gross_earnings', 10, 2);
            $table->decimal('incentives', 10, 2)->default(0.00);
            $table->decimal('fuel_expenses', 10, 2)->default(0.00);
            $table->decimal('other_expenses', 10, 2)->default(0.00);
            $table->decimal('platform_deductions', 10, 2)->default(0.00);
            $table->decimal('hours_worked', 6, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'work_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('earnings_entries');
    }
};
