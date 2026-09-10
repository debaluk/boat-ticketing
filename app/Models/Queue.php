<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Queue extends Model
{
    use HasFactory;

    protected $fillable = [
		'uuid',
		'queue_number',
		'queue_position',
		'priority',
		'customer_id',
		'service_id',
		'boat_id',
		'passenger_count',
		'queue_date',
		'queue_time',
		'status',
	];

    protected $casts = [
        'queue_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function boat(): BelongsTo
    {
        return $this->belongsTo(Boat::class);
    }
	
	protected static function booted(): void
    {
        static::creating(function (Queue $queue) {
            if (!$queue->uuid) {
                $queue->uuid = (string) Str::uuid();
            }
        });
    }

	
}