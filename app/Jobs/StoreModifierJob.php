<?php

namespace App\Jobs;

use App\Models\FoodMenu;
use App\Repositories\FoodMenuRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoreModifierJob implements ShouldQueue
{
    use Queueable;

    public $tries = 2; // Number of times the job will retry before failing
    public $backoff = 10;

    public $data;
    public $item_id;

    /**
     * Create a new job instance.
     */
    public function __construct($data, $item_id)
    {
        $this->data = $data;
        $this->item_id = $item_id;
    }

    private function prep_data($data){
        $return = [
            'name' => ucwords(strtolower($data['Description'])),
            'amount' => $data['PriceMode1'],
            'g5_id' => $data['SalesItemID'],
            'type' => 'item',
            'is_modifier' => 1,
            'group_id' => $data['GroupID'] ?? $this->item_id
        ];

        return $return;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $repo = new FoodMenuRepository(new FoodMenu());

            $prep = $this->prep_data($this->data);
            $found = $repo->findFirstBy(['g5_id' => $prep['g5_id']]);
            if(!empty($found)){
                $repo->update($found->id, $prep);
            } else {
                $prep['uuid'] = Str::uuid().'-'.time();
                $prep['is_stand_alone'] = 1;
    
                $repo->create($prep);
            }
        } catch(\Exception $e){
            Log::error('Store Modifier: '.$e->getMessage());
            throw($e);
        }
    }
}
