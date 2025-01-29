<?php

namespace App\Jobs;

use App\Models\Product;
use App\Repositories\Interfaces\MenuRepositoryInterface;
use App\Repositories\MenuRepository;
use App\Services\G5PosService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreMenuJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $repo = new MenuRepository(new Product());

        $repo->store($this->data);

        $service = new G5PosService();
        $subs = $service->getMenu([
            "ScreenID" => $this->data['ItemID'],
            "Type" => 3
        ]);

        $subs = json_decode($subs, true);
        if(count($subs) > 0){
            foreach($subs as $sub){
                $repo->store($sub);
            }
        }
    }
}
