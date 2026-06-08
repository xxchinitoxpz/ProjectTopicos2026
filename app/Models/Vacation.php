<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacation extends Model
{
    protected $fillable = [
        'staff_id',
        'date_request',
        'date_start',
        'date_end',
        'days_requested',
        'notes',
        'state',
    ];

    protected $casts = [
        'date_request' => 'date',
        'date_start' => 'date',
        'date_end' => 'date',
        'days_requested' => 'integer',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
