<?php

namespace App\Jobs;

use App\Models\EventTicketPricing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class StoreEventTicketPricingJob implements ShouldQueue
{
    use Queueable;

    private $data;

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
        $datas = $this->data;

        foreach($datas as $data){
            if(!empty($ticket = EventTicketPricing::where('g5_id', $data['ItemID'])->first())){
                $ticket->update([
                    'price' => $data['PriceMode1']
                ]);
            } else {
                EventTicketPricing::create([
                    'uuid' => Str::uuid().'-'.time(),
                    'g5_id' => $data['ItemID'],
                    'price' => $data['PriceMode1'],
                    'name' => ucwords(strtolower($data['DisplayName'])),
                    'description' => $data['Description']
                ]);
            }
        }
    }
}
