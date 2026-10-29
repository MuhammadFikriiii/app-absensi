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
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('clock_in_latitude', 10, 7)->nullable();
            $table->decimal('clock_in_longitude', 10, 7)->nullable();
            $table->unsignedInteger('clock_in_distance_meters')->nullable();
            $table->string('clock_in_ip', 45)->nullable();
            $table->string('clock_in_selfie')->nullable();
            $table->decimal('clock_out_latitude', 10, 7)->nullable();
            $table->decimal('clock_out_longitude', 10, 7)->nullable();
            $table->unsignedInteger('clock_out_distance_meters')->nullable();
            $table->string('clock_out_ip', 45)->nullable();
            $table->string('clock_out_selfie')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['clock_in_latitude', 'clock_in_longitude', 'clock_in_distance_meters', 'clock_in_ip', 'clock_in_selfie', 'clock_out_latitude', 'clock_out_longitude', 'clock_out_distance_meters', 'clock_out_ip', 'clock_out_selfie']);
        });
    }
};
