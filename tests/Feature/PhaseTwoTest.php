<?php

namespace Tests\Feature;

use App\Models\AttendanceSetting;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseTwoTest extends TestCase
{
    use RefreshDatabase;

    public function test_selfie_camera_and_private_gps_picker_are_rendered_when_configured(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employee = User::factory()->create(['role' => 'karyawan']);
        AttendanceSetting::create(['selfie_enabled' => true, 'office_radius_meters' => 150]);

        $this->actingAs($employee)->get(route('dashboard'))
            ->assertOk()->assertSee('Foto wajah langsung di aplikasi')->assertSee('Buka kamera');
        $this->actingAs($admin)->get(route('admin.attendance-settings.edit'))
            ->assertOk()->assertSee('Ambil titik lokasi')->assertSee('tidak dikirim ke layanan peta luar');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_gps_and_network_validation_accept_valid_attendance(): void
    {
        Carbon::setTestNow('2026-10-29 09:00:00');
        $user = User::factory()->create(['role' => 'karyawan']);
        AttendanceSetting::create([
            'gps_enabled' => true, 'office_latitude' => -3.319437, 'office_longitude' => 114.590752,
            'office_radius_meters' => 100, 'network_enabled' => true, 'allowed_ips' => '127.0.0.1',
        ]);

        $this->actingAs($user)->post(route('attendance.clock-in'), [
            'latitude' => -3.319437, 'longitude' => 114.590752,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', ['user_id' => $user->id, 'clock_in_distance_meters' => 0]);
    }

    public function test_gps_rejects_employee_outside_office_radius(): void
    {
        $user = User::factory()->create(['role' => 'karyawan']);
        AttendanceSetting::create(['gps_enabled' => true, 'office_latitude' => -3.319437, 'office_longitude' => 114.590752, 'office_radius_meters' => 50]);

        $this->actingAs($user)->post(route('attendance.clock-in'), [
            'latitude' => -3.300000, 'longitude' => 114.600000,
        ])->assertSessionHasErrors('attendance');

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_employee_can_submit_leave_request(): void
    {
        $user = User::factory()->create(['role' => 'karyawan']);
        $this->actingAs($user)->post(route('leaves.store'), [
            'type' => 'izin', 'start_date' => '2026-11-01', 'end_date' => '2026-11-01', 'reason' => 'Keperluan keluarga',
        ])->assertRedirect(route('leaves.index'));

        $this->assertDatabaseHas('leave_requests', ['user_id' => $user->id, 'status' => 'pending']);
    }

    public function test_admin_can_approve_leave_request(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employee = User::factory()->create(['role' => 'karyawan']);
        $leave = LeaveRequest::create(['user_id' => $employee->id, 'type' => 'cuti', 'start_date' => '2026-11-01', 'end_date' => '2026-11-02', 'reason' => 'Cuti keluarga']);

        $this->actingAs($admin)->put(route('admin.leaves.update', $leave), ['status' => 'approved'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('leave_requests', ['id' => $leave->id, 'status' => 'approved']);
        $this->assertDatabaseCount('calendar_events', 2);
    }
}
