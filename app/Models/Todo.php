<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_todo',
        'user_id',
        'title',
        'pomodoro_value',
        'time_create',
        'is_complete',
        'priority'
    ];

    protected $casts = [
        'time_create' => 'datetime',
        'is_complete' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($todo) {
            if (empty($todo->id_todo)) {
                $todo->id_todo = \Illuminate\Support\Str::uuid();
            }
        });
    }
}