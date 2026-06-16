<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanningChange extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'planning_day_id',
        'user_id',
        'change_type',
        'old_value',
        'new_value',
        'reason_type',
        'details',
    ];

    public function planningDay(): BelongsTo
    {
        return $this->belongsTo(PlanningDay::class, 'planning_day_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
