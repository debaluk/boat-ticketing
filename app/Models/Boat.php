<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Boat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'registration_number',
        'capacity',
        'status',
		'operational_status',
        'description',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}