<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Device extends Model {
 use HasUuids;
 protected $fillable=['uuid','device_code','device_name','device_type','location','status','last_seen_at'];
 protected $casts=['last_seen_at'=>'datetime'];
}
