<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder; use Illuminate\Support\Str; use Illuminate\Support\Facades\DB;
class FoundationSeeder extends Seeder { public function run():void{
foreach([['SUPER_ADMIN','Super Admin'],['ADMIN','Admin'],['CASHIER','Cashier'],['AGENT','Agent'],['POOLING_OFFICER','Pooling Officer'],['BOARDING_OFFICER','Boarding Officer'],['DISPATCH_OFFICER','Dispatch Officer'],['SUPERVISOR','Supervisor']] as [$c,$n]) DB::table('roles')->updateOrInsert(['code'=>$c],['uuid'=>(string)Str::uuid(),'name'=>$n,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()]);
foreach([['dashboard.view','View Dashboard','dashboard'],['ticket.create','Create Ticket','ticketing'],['pooling.manage','Manage Pooling','pooling'],['boarding.manage','Manage Boarding','boarding'],['dispatch.manage','Manage Dispatch','dispatch'],['master.manage','Manage Master Data','master'],['audit.view','View Audit Log','system']] as [$c,$n,$m]) DB::table('permissions')->updateOrInsert(['code'=>$c],['uuid'=>(string)Str::uuid(),'name'=>$n,'module'=>$m,'created_at'=>now(),'updated_at'=>now()]);
DB::table('devices')->updateOrInsert(['device_code'=>'LOCAL-SERVER-01'],['uuid'=>(string)Str::uuid(),'device_name'=>'Local Dermaga Server','device_type'=>'SERVER','location'=>'DERMAGA','status'=>'ACTIVE','created_at'=>now(),'updated_at'=>now()]);
}}
