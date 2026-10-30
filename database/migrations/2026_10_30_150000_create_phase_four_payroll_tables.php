<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('compensation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('position')->unique();
            $table->unsignedBigInteger('daily_transport')->default(0);
            $table->unsignedBigInteger('overtime_hourly_rate')->default(0);
            $table->unsignedBigInteger('late_deduction_per_minute')->default(0);
            $table->unsignedInteger('overtime_minimum_minutes')->default(180);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('daily_transport_override')->nullable();
            $table->unsignedBigInteger('overtime_hourly_rate_override')->nullable();
            $table->unsignedBigInteger('late_deduction_per_minute_override')->nullable();
            $table->unsignedInteger('overtime_minimum_minutes_override')->nullable();
        });

        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedInteger('attendance_days')->default(0);
            $table->unsignedInteger('overtime_minutes')->default(0);
            $table->unsignedInteger('late_minutes')->default(0);
            $table->unsignedBigInteger('transport_amount')->default(0);
            $table->unsignedBigInteger('overtime_amount')->default(0);
            $table->unsignedBigInteger('late_deduction_amount')->default(0);
            $table->bigInteger('net_amount')->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'daily_transport_override', 'overtime_hourly_rate_override',
                'late_deduction_per_minute_override', 'overtime_minimum_minutes_override',
            ]);
        });
        Schema::dropIfExists('compensation_rules');
    }
};
