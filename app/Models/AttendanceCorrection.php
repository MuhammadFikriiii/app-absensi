<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    protected $fillable = [
        'attendance_id', 'user_id', 'requested_clock_in_at', 'requested_break_started_at',
        'requested_break_ended_at', 'requested_clock_out_at', 'reason', 'attachment', 'status',
        'admin_note', 'original_values', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_clock_in_at' => 'datetime', 'requested_break_started_at' => 'datetime',
            'requested_break_ended_at' => 'datetime', 'requested_clock_out_at' => 'datetime',
            'original_values' => 'array', 'reviewed_at' => 'datetime',
        ];
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
