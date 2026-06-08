<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    protected $fillable = [
        'contract_type',
        'date_start',
        'date_end',
        'salary',
        'probation',
        'state',
        'staff_id',
    ];

    protected $casts = [
        'date_start' => 'date',
        'date_end' => 'date',
        'salary' => 'decimal:2',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
