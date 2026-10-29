<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CalendarEvent;
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
