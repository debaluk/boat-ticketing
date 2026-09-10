<?php
return [
 'sync_enabled'=>env('SYNC_ENABLED',true),
 'sync_interval_seconds'=>(int)env('SYNC_INTERVAL_SECONDS',15),
 'sync_batch_size'=>(int)env('SYNC_BATCH_SIZE',100),
 'sync_endpoint'=>env('SYNC_ENDPOINT'),
 'device_code'=>env('DEVICE_CODE','LOCAL-SERVER-01'),
];
