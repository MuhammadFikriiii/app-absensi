<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseThreeTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_submit_attendance_correction(): void
    {
        $employee = User::factory()->create(['role' => 'karyawan']);
        $attendance = Attendance::create(['user_id' => $employee->id, 'attendance_date' => '2026-10-29', 'clock_in_at' => '2026-10-29 09:00:00', 'clock_out_at' => '2026-10-29 17:00:00']);

        $this->actingAs($employee)->post(route('corrections.store', $attendance), [
            'clock_in' => '08:55', 'clock_out' => '17:05', 'reason' => 'Jam pada sistem kurang tepat.',
        ])->assertRedirect(route('corrections.index'));

        $this->assertDatabaseHas('attendance_corrections', ['attendance_id' => $attendance->id, 'status' => 'pending']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'correction_submitted']);
    }

    public function test_admin_approval_updates_attendance_and_writes_audit_log(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employee = User::factory()->create(['role' => 'karyawan']);
        $attendance = Attendance::create(['user_id' => $employee->id, 'attendance_date' => '2026-10-29', 'clock_in_at' => '2026-10-29 09:10:00', 'clock_out_at' => '2026-10-29 17:00:00']);
        $correction = AttendanceCorrection::create([
            'attendance_id' => $attendance->id, 'user_id' => $employee->id,
            'requested_clock_in_at' => '2026-10-29 08:55:00', 'requested_clock_out_at' => '2026-10-29 17:05:00',
            'reason' => 'Koreksi jam', 'original_values' => ['clock_in_at' => '2026-10-29 09:10:00', 'clock_out_at' => '2026-10-29 17:00:00'],
        ]);

        $this->actingAs($admin)->put(route('admin.corrections.update', $correction), ['status' => 'approved', 'admin_note' => 'Bukti sesuai.'])
            ->assertSessionHas('success');

        $this->assertSame('08:55', $attendance->refresh()->clock_in_at->format('H:i'));
        $this->assertDatabaseHas('attendance_corrections', ['id' => $correction->id, 'status' => 'approved']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'correction_approved']);
    }

    public function test_admin_can_open_daily_weekly_and_monthly_recaps(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employee = User::factory()->create(['role' => 'karyawan', 'name' => 'Pegawai Rekap']);
        Attendance::create(['user_id' => $employee->id, 'attendance_date' => '2026-10-29', 'clock_in_at' => '2026-10-29 09:00:00', 'clock_out_at' => '2026-10-29 17:00:00']);
        foreach (['daily', 'weekly', 'monthly'] as $period) {
            $this->actingAs($admin)->get(route('admin.recaps.index', ['period' => $period, 'date' => '2026-10-29']))
                ->assertOk()->assertSee('Rekap absensi')->assertSee('Pegawai Rekap');
        }
    }

    public function test_employee_cannot_open_admin_recap_or_audit_log(): void
    {
        $employee = User::factory()->create(['role' => 'karyawan']);
        $this->actingAs($employee)->get(route('admin.recaps.index'))->assertForbidden();
        $this->actingAs($employee)->get(route('admin.audit-logs.index'))->assertForbidden();
    }
}
