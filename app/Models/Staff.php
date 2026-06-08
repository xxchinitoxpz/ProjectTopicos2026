<?php

namespace App\Models;

use App\Support\PublicImageStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'dni',
        'name',
        'last_name',
        'email',
        'birthdate',
        'phone',
        'address',
        'vacation_days',
        'photo',
        'staff_type_id',
        'status',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public function getPhotoUrlAttribute(): string
    {
        return PublicImageStorage::url($this->photo);
    }

    public function staffType(): BelongsTo
    {
        return $this->belongsTo(StaffType::class);
    }

    public function contracts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function vacations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Vacation::class);
    }

    public function assistances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Assistance::class);
    }

    public function hasActiveEligibleContract(): bool
    {
        return $this->contracts()
            ->where('state', 'active')
            ->whereIn('contract_type', ['permanente', 'nombrado'])
            ->exists();
    }

    public function hasVacationOverlap(string $startDate, string $endDate, ?int $excludeVacationId = null): bool
    {
        $query = $this->vacations()
            ->whereIn('state', ['pending', 'approved'])
            ->where('date_start', '<=', $endDate)
            ->where('date_end', '>=', $startDate);

        if ($excludeVacationId) {
            $query->where('id', '!=', $excludeVacationId);
        }

        return $query->exists();
    }
}
