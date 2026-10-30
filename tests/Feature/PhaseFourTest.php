<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\CompensationRule;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Services\PayrollCalculator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseFourTest extends TestCase
{
    use RefreshDatabase;

    public function test_payroll_calculates_transport_overtime_and_late_deduction(): void
    {
        $employee = User::factory()->create(['role' => 'karyawan', 'position' => 'Staf Tes']);
        CompensationRule::create([
            'position' => 'Staf Tes', 'daily_transport' => 50000, 'overtime_hourly_rate' => 30000,
            'late_deduction_per_minute' => 1000, 'overtime_minimum_minutes' => 180,
        ]);
        WorkSchedule::create(['day_of_week' => 1, 'start_time' => '09:00', 'end_time' => '17:00', 'is_workday' => true, 'tolerance_minutes' => 10]);
        Attendance::create([
            'user_id' => $employee->id, 'attendance_date' => '2026-10-05',
            'clock_in_at' => '2026-10-05 09:20:00', 'clock_out_at' => '2026-10-05 20:15:00',
            'late_minutes' => 10, 'status' => 'hadir',
        ]);

        $result = app(PayrollCalculator::class)->calculate($employee, Carbon::parse('2026-10-01'), Carbon::parse('2026-10-31'));

        $this->assertSame(1, $result['attendance_days']);
        $this->assertSame(195, $result['overtime_minutes']);
        $this->assertSame(50000, $result['transport_amount']);
        $this->assertSame(97500, $result['overtime_amount']);
        $this->assertSame(10000, $result['late_deduction_amount']);
        $this->assertSame(137500, $result['net_amount']);
    }

    public function test_admin_can_mark_monthly_payment_as_paid(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $employee = User::factory()->create(['role' => 'karyawan', 'position' => 'Staf Tes']);
        CompensationRule::create(['position' => 'Staf Tes', 'daily_transport' => 40000, 'overtime_hourly_rate' => 0, 'late_deduction_per_minute' => 0, 'overtime_minimum_minutes' => 180]);
        Attendance::create(['user_id' => $employee->id, 'attendance_date' => '2026-10-05', 'clock_in_at' => '2026-10-05 09:00:00', 'clock_out_at' => '2026-10-05 17:00:00']);

        $this->actingAs($admin)->post(route('admin.payrolls.pay', $employee), ['month' => '2026-10'])->assertSessionHas('success');
        $this->assertDatabaseHas('payroll_periods', ['user_id' => $employee->id, 'status' => 'paid', 'transport_amount' => 40000, 'net_amount' => 40000]);
    }

    public function test_employee_cannot_access_payroll_admin_pages(): void
    {
        $employee = User::factory()->create(['role' => 'karyawan']);
        $this->actingAs($employee)->get(route('admin.payrolls.index'))->assertForbidden();
        $this->actingAs($employee)->get(route('admin.compensations.edit'))->assertForbidden();
    }
}
