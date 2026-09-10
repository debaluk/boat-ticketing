<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'countries';

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'status',
    ];

    public function customers()
    {
        return $this->hasMany(
            Customer::class,
            'country_id',
            'id'
        );
    }
}