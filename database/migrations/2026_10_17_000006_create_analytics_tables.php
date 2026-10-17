<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Gigy 2.0 Analytics & Intelligence Platform.
     */
    public function up(): void
    {
        // 1. Companies / Delivery Platforms
        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('slug')->unique();
                $table->string('category')->default('Food Delivery'); // Food Delivery, Quick Commerce, Ride-hail, Courier
                $table->string('color', 10)->default('#059669');
                $table->boolean('is_demo')->default(false)->index();
                $table->timestamps();
            });
        }

        // 2. Work Sessions
        if (!Schema::hasTable('work_sessions')) {
            Schema::create('work_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->foreignId('earnings_entry_id')->nullable()->constrained('earnings_entries')->nullOnDelete();
                $table->dateTime('start_time')->index();
                $table->dateTime('end_time')->index();
                $table->decimal('total_hours', 6, 2);
                $table->integer('deliveries_completed')->default(0);
                $table->decimal('gross_earnings', 10, 2)->default(0.00);
                $table->decimal('net_earnings', 10, 2)->default(0.00);
                $table->decimal('hourly_rate', 10, 2)->default(0.00);
                $table->string('city', 50)->default('Bengaluru')->index();
                $table->string('worker_role', 50)->default('Food Delivery Partner')->index();
                $table->boolean('is_demo')->default(false)->index();
                $table->timestamps();

                $table->index(['user_id', 'start_time']);
                $table->index(['city', 'worker_role']);
            });
        }

        // 3. Deliveries
        if (!Schema::hasTable('deliveries')) {
            Schema::create('deliveries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('work_session_id')->nullable()->constrained('work_sessions')->nullOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->string('delivery_identifier', 40);
                $table->dateTime('pickup_time')->index();
                $table->dateTime('dropoff_time')->nullable();
                $table->integer('duration_minutes')->default(20);
                $table->decimal('distance_km', 5, 2)->default(3.50);
                $table->decimal('earnings', 8, 2)->default(0.00);
                $table->decimal('tip_amount', 8, 2)->default(0.00);
                $table->string('status', 20)->default('completed')->index(); // completed, cancelled
                $table->string('city', 50)->default('Bengaluru')->index();
                $table->boolean('is_demo')->default(false)->index();
                $table->timestamps();

                $table->index(['user_id', 'pickup_time']);
            });
        }

        // 4. Earnings Goals
        if (!Schema::hasTable('earnings_goals')) {
            Schema::create('earnings_goals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('type', 20)->default('weekly'); // daily, weekly, monthly
                $table->decimal('target_amount', 10, 2);
                $table->date('start_date');
                $table->date('end_date');
                $table->string('status', 20)->default('active'); // active, completed, archived
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'status', 'start_date']);
            });
        }

        // 5. Enhance Users table with city & demo flag
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city', 50)->default('Bengaluru')->after('worker_type');
            }
            if (!Schema::hasColumn('users', 'is_demo')) {
                $table->boolean('is_demo')->default(false)->after('city')->index();
            }
        });

        // 6. Enhance Earnings Entries with analytics attributes
        Schema::table('earnings_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('earnings_entries', 'company_name')) {
                $table->string('company_name', 50)->nullable()->after('work_date');
            }
            if (!Schema::hasColumn('earnings_entries', 'city')) {
                $table->string('city', 50)->default('Bengaluru')->after('company_name');
            }
            if (!Schema::hasColumn('earnings_entries', 'worker_role')) {
                $table->string('worker_role', 50)->default('Food Delivery Partner')->after('city');
            }
            if (!Schema::hasColumn('earnings_entries', 'deliveries_count')) {
                $table->integer('deliveries_count')->default(0)->after('worker_role');
            }
            if (!Schema::hasColumn('earnings_entries', 'is_demo')) {
                $table->boolean('is_demo')->default(false)->after('deliveries_count')->index();
            }
        });

        // 7. Enhance Complaints with analytics resolution metrics
        Schema::table('complaints', function (Blueprint $table) {
            if (!Schema::hasColumn('complaints', 'city')) {
                $table->string('city', 50)->default('Bengaluru')->after('company_name');
            }
            if (!Schema::hasColumn('complaints', 'worker_role')) {
                $table->string('worker_role', 50)->default('Food Delivery Partner')->after('city');
            }
            if (!Schema::hasColumn('complaints', 'resolution_time_hours')) {
                $table->integer('resolution_time_hours')->nullable()->after('status');
            }
            if (!Schema::hasColumn('complaints', 'resolved_at')) {
                $table->dateTime('resolved_at')->nullable()->after('resolution_time_hours');
            }
            if (!Schema::hasColumn('complaints', 'is_demo')) {
                $table->boolean('is_demo')->default(false)->after('resolved_at')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('earnings_goals');
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('work_sessions');
        Schema::dropIfExists('companies');

        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['city', 'worker_role', 'resolution_time_hours', 'resolved_at', 'is_demo']);
        });

        Schema::table('earnings_entries', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'city', 'worker_role', 'deliveries_count', 'is_demo']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['city', 'is_demo']);
        });
    }
};
