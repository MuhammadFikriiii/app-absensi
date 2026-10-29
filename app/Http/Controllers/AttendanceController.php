<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function clockIn(Request $request)
    {
        $user = $request->user();
        $now = Carbon::now();
        $evidence = $this->validateEvidence($request, 'clock_in');

        DB::transaction(function () use ($user, $now, $evidence) {
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('attendance_date', $now->toDateString())
                ->first() ?? new Attendance([
                    'user_id' => $user->id,
                    'attendance_date' => $now->toDateString(),
                ]);

            if ($attendance->clock_in_at) {
                throw ValidationException::withMessages(['attendance' => 'Anda sudah absen masuk hari ini.']);
            }

            $schedule = WorkSchedule::where('day_of_week', $now->isoWeekday())
                ->where(fn ($query) => $query->where('user_id', $user->id)->orWhereNull('user_id'))
                ->orderByRaw('user_id IS NULL')
                ->first();

            $lateMinutes = 0;
            if ($schedule?->is_workday && $schedule->start_time) {
                $start = Carbon::parse($now->toDateString().' '.$schedule->start_time)
                    ->addMinutes($schedule->tolerance_minutes);
                $lateMinutes = $now->greaterThan($start) ? (int) $start->diffInMinutes($now) : 0;
            }

            $attendance->fill([
                'clock_in_at' => $now,
                'status' => 'hadir',
                'late_minutes' => $lateMinutes,
                ...$evidence,
            ])->save();
        });

        return back()->with('success', 'Absen masuk berhasil dicatat.');
    }

    public function clockOut(Request $request)
    {
        $attendance = $this->todayAttendance($request);
        if (! $attendance->clock_in_at) {
            return $this->error('Silakan absen masuk terlebih dahulu.');
        }
        if ($attendance->clock_out_at) {
            return $this->error('Anda sudah absen keluar hari ini.');
        }
        if ($attendance->break_started_at && ! $attendance->break_ended_at) {
            return $this->error('Selesaikan istirahat sebelum absen keluar.');
        }

        $attendance->update(['clock_out_at' => now(), ...$this->validateEvidence($request, 'clock_out')]);

        return back()->with('success', 'Absen keluar berhasil dicatat.');
    }

    public function startBreak(Request $request)
    {
        $attendance = $this->todayAttendance($request);
        if (! $attendance->clock_in_at || $attendance->clock_out_at) {
            return $this->error('Istirahat hanya tersedia saat jam kerja aktif.');
        }
        if ($attendance->break_started_at) {
            return $this->error('Istirahat hari ini sudah pernah dimulai.');
        }

        $attendance->update(['break_started_at' => now()]);

        return back()->with('success', 'Waktu istirahat dimulai.');
    }

    public function endBreak(Request $request)
    {
        $attendance = $this->todayAttendance($request);
        if (! $attendance->break_started_at || $attendance->break_ended_at) {
            return $this->error('Tidak ada istirahat aktif yang dapat diselesaikan.');
        }

        $attendance->update(['break_ended_at' => now()]);

        return back()->with('success', 'Istirahat selesai. Selamat melanjutkan pekerjaan.');
    }

    private function todayAttendance(Request $request): Attendance
    {
        return Attendance::where('user_id', $request->user()->id)
            ->whereDate('attendance_date', now()->toDateString())
            ->first() ?? new Attendance([
                'user_id' => $request->user()->id,
                'attendance_date' => now()->toDateString(),
            ]);
    }

    private function error(string $message)
    {
        return back()->withErrors(['attendance' => $message]);
    }

    private function validateEvidence(Request $request, string $prefix): array
    {
        $settings = AttendanceSetting::current();
        $rules = [];
        if ($settings->gps_enabled) {
            $rules['latitude'] = ['required', 'numeric', 'between:-90,90'];
            $rules['longitude'] = ['required', 'numeric', 'between:-180,180'];
        }
        if ($settings->selfie_enabled) {
            $rules['selfie'] = ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'];
        }
        $data = $request->validate($rules, [
            'latitude.required' => 'Lokasi belum didapatkan. Aktifkan GPS lalu coba kembali.',
            'selfie.required' => 'Selfie wajib diambil sebelum absensi.',
        ]);

        $result = [$prefix.'_ip' => $request->ip()];
        if ($settings->network_enabled && ! $this->ipAllowed($request->ip(), (string) $settings->allowed_ips)) {
            throw ValidationException::withMessages(['attendance' => 'Perangkat tidak terhubung ke jaringan kampus yang diizinkan. IP Anda: '.$request->ip()]);
        }
        if ($settings->gps_enabled) {
            $distance = $this->distanceMeters((float) $data['latitude'], (float) $data['longitude'], (float) $settings->office_latitude, (float) $settings->office_longitude);
            if ($distance > $settings->office_radius_meters) {
                throw ValidationException::withMessages(['attendance' => 'Anda berada '.number_format($distance).' meter dari kantor, di luar radius '.$settings->office_radius_meters.' meter.']);
            }
            $result[$prefix.'_latitude'] = $data['latitude'];
            $result[$prefix.'_longitude'] = $data['longitude'];
            $result[$prefix.'_distance_meters'] = (int) round($distance);
        }
        if ($settings->selfie_enabled) {
            $result[$prefix.'_selfie'] = $request->file('selfie')->store('attendance-selfies/'.now()->format('Y/m'), 'public');
        }

        return $result;
    }

    private function distanceMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earth = 6371000;
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        $a = sin($latDelta / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lonDelta / 2) ** 2;

        return $earth * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function ipAllowed(string $ip, string $allowed): bool
    {
        $entries = preg_split('/[\s,]+/', trim($allowed), -1, PREG_SPLIT_NO_EMPTY);
        foreach ($entries as $entry) {
            if ($entry === $ip) {
                return true;
            }
            if (str_contains($entry, '/')) {
                [$network, $mask] = explode('/', $entry, 2);
                $ipLong = ip2long($ip);
                $networkLong = ip2long($network);
                if ($ipLong !== false && $networkLong !== false && ($ipLong & (-1 << (32 - (int) $mask))) === ($networkLong & (-1 << (32 - (int) $mask)))) {
                    return true;
                }
            }
        }

        return false;
    }
}
