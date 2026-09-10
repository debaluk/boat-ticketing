<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'phone',
        'email',
        'address',
        'country_id',
        'status',
    ];

    public function country()
    {
        return $this->belongsTo(
            Country::class,
            'country_id'
        );
    }
}