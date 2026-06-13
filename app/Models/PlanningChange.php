<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanningChange extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'planning_id',
        'user_id',
        'action',
        'details',
    ];

    public function planning(): BelongsTo
    {
        return $this->belongsTo(Planning::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
