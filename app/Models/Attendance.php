<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id', 'attendance_date', 'clock_in_at', 'clock_out_at',
        'break_started_at', 'break_ended_at', 'status', 'late_minutes',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'clock_in_at' => 'datetime',
            'clock_out_at' => 'datetime',
            'break_started_at' => 'datetime',
            'break_ended_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStateAttribute(): string
    {
        if ($this->clock_out_at) return 'selesai';
        if ($this->break_started_at && ! $this->break_ended_at) return 'istirahat';
        if ($this->clock_in_at) return 'bekerja';
        return 'belum_masuk';
    }
}
