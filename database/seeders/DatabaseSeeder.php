<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\CalendarEvent;
use App\Models\CompensationRule;
use App\Models\User;
use App\Models\WorkSchedule;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@absensi.test'], [
            'name' => 'Admin Absensi', 'password' => 'password', 'role' => 'admin',
            'position' => 'Administrator', 'is_active' => true,
        ]);

        $employee = User::updateOrCreate(['email' => 'budi@absensi.test'], [
            'name' => 'Budi Santoso', 'password' => 'password', 'role' => 'karyawan',
            'phone' => '085248867449', 'position' => 'Staf Akademik',
            'birth_date' => '1994-11-05', 'is_active' => true,
        ]);

        User::updateOrCreate(['email' => 'siti@absensi.test'], [
            'name' => 'Siti Rahma', 'password' => 'password', 'role' => 'karyawan',
            'phone' => '081234567890', 'position' => 'Staf Administrasi',
            'birth_date' => '1996-12-12', 'is_active' => true,
        ]);

        CompensationRule::updateOrCreate(['position' => 'Staf Akademik'], [
            'daily_transport' => 50000, 'overtime_hourly_rate' => 25000,
            'late_deduction_per_minute' => 1000, 'overtime_minimum_minutes' => 180,
        ]);
        CompensationRule::updateOrCreate(['position' => 'Staf Administrasi'], [
            'daily_transport' => 45000, 'overtime_hourly_rate' => 25000,
            'late_deduction_per_minute' => 1000, 'overtime_minimum_minutes' => 180,
        ]);

        AttendanceSetting::firstOrCreate([], [
            'gps_enabled' => false, 'network_enabled' => false, 'selfie_enabled' => false,
            'office_radius_meters' => 150, 'allowed_ips' => "127.0.0.1\n::1",
        ]);

        $sampleDate = now()->subDay()->toDateString();
        Attendance::updateOrCreate(['user_id' => $employee->id, 'attendance_date' => $sampleDate], [
            'clock_in_at' => $sampleDate.' 09:03:00', 'break_started_at' => $sampleDate.' 12:00:00',
            'break_ended_at' => $sampleDate.' 13:00:00', 'clock_out_at' => $sampleDate.' 17:04:00',
            'status' => 'hadir', 'late_minutes' => 0, 'clock_in_ip' => '127.0.0.1', 'clock_out_ip' => '127.0.0.1',
        ]);

        foreach (range(1, 7) as $day) {
            WorkSchedule::updateOrCreate(
                ['user_id' => null, 'day_of_week' => $day],
                ['start_time' => '09:00', 'end_time' => '17:00', 'is_workday' => $day <= 5, 'tolerance_minutes' => 10],
            );
        }

        CalendarEvent::updateOrCreate(['title' => 'Libur kantor contoh'], [
            'event_date' => now()->addDays(7)->toDateString(), 'type' => 'holiday',
            'description' => 'Contoh agenda kalender Phase 1.',
        ]);
    }
}
