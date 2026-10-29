<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    protected $fillable = ['gps_enabled', 'office_latitude', 'office_longitude', 'office_radius_meters', 'network_enabled', 'allowed_ips', 'selfie_enabled'];

    protected function casts(): array
    {
        return ['gps_enabled' => 'boolean', 'network_enabled' => 'boolean', 'selfie_enabled' => 'boolean'];
    }

    public static function current(): self
    {
        return static::firstOrCreate([]);
    }
}
