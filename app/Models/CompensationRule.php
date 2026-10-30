<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompensationRule extends Model
{
    protected $fillable = ['position', 'daily_transport', 'overtime_hourly_rate', 'late_deduction_per_minute', 'overtime_minimum_minutes'];
}
