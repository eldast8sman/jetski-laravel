<?php

namespace App\Jobs;

use App\Models\FoodMenu;
use App\Repositories\FoodMenuRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoreFoodMenuJobManul implements ShouldQueue
{
    use Queueable;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    private function prep_data($data){
        $return = [
            'uuid' => Str::uuid().'-'.time(),
            'name' => ucwords(strtolower($data['DisplayName'])),
            'amount' => $data['PriceMode1'],
            'g5_id' => $data['ItemID'],
            'parent_id' => $data['ParentID'],
            'modifier_id' => $data['Modifier1'],
            'type' => ($data['ItemTypeID'] == 3) ? 'screen' : 'item'
        ];

        if((strtolower($data['DisplayName']) == 'add on') or (strtolower(substr($data['DisplayName'], 0, 4)) == 'add ')){
            $return['is_add_on'] = 1;
        }
        if(strpos(strtolower($data['DisplayName']), 'delivery') !== false){
            $return['is_delivery_fee'] = 1;
            $return['is_new'] = 0;
        } else {
            $return['is_delivery_fee'] = 0;
        }

        return $return;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $repo = new FoodMenuRepository(new FoodMenu());

            if($this->data['ItemID'] == 53){
                exit;
            }
    
            $found = $repo->findFirstBy(['g5_id' => $this->data['ItemID']]);
            if(!empty($found)){
                $update_data = ['amount' => $this->data['PriceMode1']];
                if(strpos(strtolower($this->data['DisplayName']), 'delivery') !== false){
                    $update_data['is_delivery_fee'] = 1;
                    $update_data['is_new'] = 0;
                }
                $repo->update($found->id, $update_data);
            } else {
                $data = $this->prep_data($this->data);
                $repo->create($data);
            }
        } catch(\Exception $e){
            Log::error("Menu job: ".$e->getMessage());
            throw($e);
        }
    }
}
