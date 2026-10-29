<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    protected $fillable = ['user_id', 'title', 'event_date', 'type', 'description'];

    protected function casts(): array
    {
        return ['event_date' => 'date'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
