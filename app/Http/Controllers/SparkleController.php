<?php

namespace App\Http\Controllers;

use App\Jobs\SaveSparkleTransactionJob;
use App\Jobs\SparkleWebhookJob;
use App\Jobs\UpdateWalletTransactionsJob;
use App\Models\TransactionTracker;
use App\Models\WalletTransaction;
use App\Services\SparkleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SparkleController extends Controller
{  
    private $sparkle;

    public function __construct(SparkleService $service)
    {
        $this->sparkle = $service;
    }

    public function webhook(Request $request){
        SparkleWebhookJob::dispatch($request->all());
        return $this->success_response("Webhook received");
    }

    public function update_uncredited(){
        $transactions = WalletTransaction::where('type', 'Credit')->where('is_user_credited', false);
        if($transactions->count() < 1){
            return;
        }

        foreach($transactions->get() as $transaction){
            dispatch(new UpdateWalletTransactionsJob($transaction));
        }
    }

    public function fetch_transactions(){
        $this->update_uncredited();
        $tracker = TransactionTracker::orderBy('id', 'desc')->first();
        $start_date = "";
        $end_date = "";
        if(!empty($tracker)){
            $start_date = $tracker->last_sync;
            $end_date = Carbon::now('Africa/Lagos')->format('Y-m-d');
        }
        $transactions = $this->sparkle->getTransactions($start_date, $end_date);
        if(empty($transactions)){
            return $this->failed_response("No Pending Transaction yet");
        }
        
        foreach($transactions['data'] as $transaction){
            if($transaction['type'] != 'Credit'){
                continue;
            }
            dispatch(new  SaveSparkleTransactionJob($transaction));
        }

        if(empty($tracker)){
            TransactionTracker::create([
                'last_sync' => Carbon::now('Africa/Lagos')->format('Y-m-d')
            ]);
        } else {
            $tracker->last_sync = Carbon::now('Africa/Lagos')->format('Y-m-d');
            $tracker->save();
        }

        return $this->success_response("Transactions Update in Progress");
    
    }

    public function customers(){
        $customers = $this->sparkle->getCustomers();
        if(empty($customers)){
            return $this->failed_response("No Customers yet");
        }
        return $this->success_response("Customers fetched successfully", $customers);
    }
}
