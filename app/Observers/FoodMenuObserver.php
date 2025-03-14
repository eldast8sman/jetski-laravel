<?php

namespace App\Observers;

use App\Jobs\StoreModifierJob;
use App\Models\FoodMenu;
use App\Services\G5PosService;

class FoodMenuObserver
{
    /**
     * Handle the FoodMenu "created" event.
     */
    public function created(FoodMenu $foodMenu): void
    {
        if(!empty($foodMenu->modifier_id)){
            $g5 = new G5PosService();
            $modifiers = $g5->getModifiers($foodMenu->modifier_id);

            $modifiers = json_decode($modifiers, true);
            foreach($modifiers as $modifier){
                StoreModifierJob::dispatch($modifier, $foodMenu->modifier_id);
            }
        }
    }

    /**
     * Handle the FoodMenu "updated" event.
     */
    public function updated(FoodMenu $foodMenu): void
    {
        if(!empty($foodMenu->modifier_id)){
            $g5 = new G5PosService();
            $modifiers = $g5->getModifiers($foodMenu->modifier_id);

            $modifiers = json_decode($modifiers, true);
            foreach($modifiers as $modifier){
                StoreModifierJob::dispatch($modifier, $foodMenu->modifier_id);
            }
        }
    }

    /**
     * Handle the FoodMenu "deleted" event.
     */
    public function deleted(FoodMenu $foodMenu): void
    {
        //
    }

    /**
     * Handle the FoodMenu "restored" event.
     */
    public function restored(FoodMenu $foodMenu): void
    {
        //
    }

    /**
     * Handle the FoodMenu "force deleted" event.
     */
    public function forceDeleted(FoodMenu $foodMenu): void
    {
        //
    }
}
