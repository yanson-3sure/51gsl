<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Services\API\DyxcService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class DyxcSyncJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $status_id;
    protected $newsType;
    /**
     * Create a new job instance.
     *  php artisan queue:work --daemon --sleep=1 --tries=3
     * @return void
     */
    public function __construct($status_id ,$newsType)
    {
        $this->status_id = $status_id;
        $this->newsType = $newsType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DyxcService $service)
    {
        $service->sync($this->status_id,$this->newsType);
    }
}
