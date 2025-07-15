<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WilayahService;

class SyncWilayah extends Command
{
    protected $signature = 'wilayah:sync';
    protected $description = 'Sync wilayah data from API to local database';

    public function handle()
    {
        $this->info('Starting wilayah synchronization...');

        $wilayahService = new WilayahService();
        $result = $wilayahService->syncAllWilayah();

        if ($result) {
            $this->info('Wilayah data synchronized successfully!');
        } else {
            $this->error('Failed to synchronize wilayah data!');
        }
    }
}
