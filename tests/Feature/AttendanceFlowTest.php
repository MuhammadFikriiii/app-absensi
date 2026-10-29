<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_employee_must_follow_attendance_sequence_without_duplicate_actions(): void
    {
        Carbon::setTestNow('2026-10-29 09:08:00');
        $user = User::factory()->create(['role' => 'karyawan', 'is_active' => true]);
        WorkSchedule::create([
            'day_of_week' => 4, 'start_time' => '09:00', 'end_time' => '17:00',
            'is_workday' => true, 'tolerance_minutes' => 5,
        ]);

        $this->actingAs($user)->post(route('attendance.clock-out'))->assertSessionHasErrors('attendance');
        $this->assertDatabaseCount('attendances', 0);

        $this->actingAs($user)->post(route('attendance.clock-in'))->assertSessionHas('success');
        $this->actingAs($user)->post(route('attendance.clock-in'))->assertSessionHasErrors('attendance');

        $attendance = Attendance::first();
        $this->assertSame(3, $attendance->late_minutes);

        $this->actingAs($user)->post(route('attendance.break.start'))->assertSessionHas('success');
        $this->actingAs($user)->post(route('attendance.clock-out'))->assertSessionHasErrors('attendance');
        $this->actingAs($user)->post(route('attendance.break.end'))->assertSessionHas('success');
        $this->actingAs($user)->post(route('attendance.clock-out'))->assertSessionHas('success');

        $attendance->refresh();
        $this->assertNotNull($attendance->clock_in_at);
        $this->assertNotNull($attendance->break_started_at);
        $this->assertNotNull($attendance->break_ended_at);
        $this->assertNotNull($attendance->clock_out_at);
    }
}
