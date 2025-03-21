<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class BulkJobHandler implements ShouldQueue
{
    use Queueable;

    private $data;
    private $type;

    /**
     * Create a new job instance.
     */
    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if($this->type == 'g5_members'){
            foreacH($this->data as $data){
                dispatch(new SaveG5MembersJob($data));
            }
        }
    }
}
