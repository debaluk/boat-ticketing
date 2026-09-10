<?php
namespace App\Services;
use App\Models\SyncTransaction;
use Illuminate\Support\Facades\Http;
class OfflineSyncService {
 public function pushPending(): int {
  $endpoint=config('offline.sync_endpoint');
  if(!config('offline.sync_enabled')||!$endpoint)return 0;
  $items=SyncTransaction::where('status','PENDING')->orderBy('id')->limit(config('offline.sync_batch_size'))->get();
  $synced=0;
  foreach($items as $item){
   $item->update(['status'=>'SYNCING','attempts'=>$item->attempts+1,'last_attempt_at'=>now()]);
   try {
    $r=Http::timeout(5)->post($endpoint,['device_code'=>config('offline.device_code'),'transaction'=>$item->toArray()]);
    if($r->successful()){ $item->update(['status'=>'SYNCED','synced_at'=>now(),'error_message'=>null]); $synced++; }
    else $item->update(['status'=>'FAILED','error_message'=>'HTTP '.$r->status()]);
   } catch(\Throwable $e){$item->update(['status'=>'FAILED','error_message'=>$e->getMessage()]);}
  }
  return $synced;
 }
}
