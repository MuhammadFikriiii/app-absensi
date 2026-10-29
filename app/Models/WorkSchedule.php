<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    protected $fillable = [
        'user_id', 'day_of_week', 'start_time', 'end_time', 'is_workday', 'tolerance_minutes',
    ];

    protected function casts(): array
    {
        return ['is_workday' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
