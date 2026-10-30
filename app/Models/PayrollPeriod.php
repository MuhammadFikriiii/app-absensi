<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    protected $fillable = [
        'user_id', 'period_start', 'period_end', 'attendance_days', 'overtime_minutes',
        'late_minutes', 'transport_amount', 'overtime_amount', 'late_deduction_amount',
        'net_amount', 'status', 'paid_at', 'paid_by',
    ];

    protected function casts(): array
    {
        return ['period_start' => 'date', 'period_end' => 'date', 'paid_at' => 'datetime'];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function payer() { return $this->belongsTo(User::class, 'paid_by'); }
}
