<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'boat_id',
        'route_id',
        'departure_date',
        'departure_time',
        'arrival_time',
        'price',
        'capacity',
        'available_seats',
        'status',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'price' => 'decimal:2',
    ];

    public function boat(): BelongsTo
    {
        return $this->belongsTo(Boat::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }
}