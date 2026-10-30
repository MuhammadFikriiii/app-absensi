<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'action', 'auditable_type', 'auditable_id', 'description', 'old_values', 'new_values', 'ip_address', 'user_agent', 'created_at'];

    protected function casts(): array
    {
        return ['old_values' => 'array', 'new_values' => 'array', 'created_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    public static function record(string $action, Model $model, string $description, ?array $old = null, ?array $new = null): self
    {
        return static::create([
            'user_id' => auth()->id(), 'action' => $action, 'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(), 'description' => $description,
            'old_values' => $old, 'new_values' => $new,
            'ip_address' => request()?->ip(), 'user_agent' => request()?->userAgent(), 'created_at' => now(),
        ]);
    }
}
