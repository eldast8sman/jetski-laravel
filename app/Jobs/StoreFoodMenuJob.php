<?php

namespace App\Jobs;

use App\Models\FoodMenu;
use App\Repositories\FoodMenuRepository;
use App\Services\G5PosService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class StoreFoodMenuJob implements ShouldQueue
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
            'modifier_id' => $data['Modifier1']
        ];

        if((strtolower($data['DisplayName']) == 'add on') or (strtolower(substr($data['DisplayName'], 0, 4)) == 'add ')){
            $return['is_add_on'] = 1;
        }

        return $return;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $repo = new FoodMenuRepository(new FoodMenu());

        if($this->data['ItemID'] == 53){
            exit;
        }

        $found = $repo->findFirstBy(['g5_id' => $this->data['ItemID']]);
        if(!empty($found)){
            $repo->update($found->id, ['amount' => $this->data['PriceMode1']]);
        } else {
            $data = $this->prep_data($this->data);
            $repo->create($data);
        }

        $service = new G5PosService();
        $subs = $service->getMenu([
            'ScreenID' => $this->data['ItemID'],
            'Type' => 3
        ]);

        $subs = json_decode($subs, true);
        if(count($subs) > 0){
            foreach($subs as $sub){
                $found = $repo->findFirstBy(['g5_id' => $sub['ItemID']]);
                if(!empty($found)){
                    $repo->update($found->id, ['amount' => $sub['PriceMode1']]);
                } else {
                    $data = $this->prep_data($sub);
                    $repo->create($data);
                }
            }
        }
    }
}
