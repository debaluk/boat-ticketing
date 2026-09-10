<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class AuditLog extends Model {
 use HasUuids; public $timestamps=false;
 protected $fillable=['uuid','user_id','device_id','action','entity_type','entity_id','old_values','new_values','ip_address','user_agent','created_at'];
 protected $casts=['old_values'=>'array','new_values'=>'array','created_at'=>'datetime'];
}
