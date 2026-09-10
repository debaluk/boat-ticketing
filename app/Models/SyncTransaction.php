<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncTransaction extends Model
{
    use HasFactory;

	protected $fillable = [
		'uuid',
		'device_id',
		'transaction_type',
		'entity_type',
		'entity_id',
		'payload',
		'status',
		'attempts',
		'last_attempt_at',
		'synced_at',
		'error_message',
	];

    protected $casts = [
		'payload' => 'array',
		'last_attempt_at' => 'datetime',
		'synced_at' => 'datetime',
	];
}