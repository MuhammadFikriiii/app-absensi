<?php

namespace Database\Seeders;

use App\Models\AttendanceSetting;
use App\Models\CalendarEvent;
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

        User::updateOrCreate(['email' => 'budi@absensi.test'], [
            'name' => 'Budi Santoso', 'password' => 'password', 'role' => 'karyawan',
            'phone' => '085248867449', 'position' => 'Staf Akademik',
            'birth_date' => '1994-11-05', 'is_active' => true,
        ]);

        User::updateOrCreate(['email' => 'siti@absensi.test'], [
            'name' => 'Siti Rahma', 'password' => 'password', 'role' => 'karyawan',
            'phone' => '081234567890', 'position' => 'Staf Administrasi',
            'birth_date' => '1996-12-12', 'is_active' => true,
        ]);

        AttendanceSetting::firstOrCreate([], [
            'gps_enabled' => false, 'network_enabled' => false, 'selfie_enabled' => false,
            'office_radius_meters' => 150, 'allowed_ips' => "127.0.0.1\n::1",
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
