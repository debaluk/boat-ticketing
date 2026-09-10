<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
		'uuid',
		'ticket_number',
		'queue_id',
		'customer_id',
		'service_id',
		'passenger_count',
		'price',
		'subtotal',
		'discount',
		'total',
		'status',
	];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function queue(): BelongsTo
    {
        return $this->belongsTo(Queue::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
	public function payment(): HasOne
	{
		return $this->hasOne(Payment::class);
	}
}