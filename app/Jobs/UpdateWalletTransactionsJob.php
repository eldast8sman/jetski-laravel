<?php

namespace App\Jobs;

use App\Mail\CreditNotificationMail;
use App\Models\WalletTransaction;
use App\Services\G5PosService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class UpdateWalletTransactionsJob implements ShouldQueue
{
    use Queueable;

    private $transaction;
    private $g5;

    /**
     * Create a new job instance.
     */
    public function __construct(WalletTransaction $transaction)
    {
        $this->transaction = $transaction;
        $this->g5  = new G5PosService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $wallet = $this->transaction->wallet()->first();
        $user = $wallet->user()->first();
        if(!empty($user->g5_id)){
            $response = (env('APP_ENV') == 'production') ? $this->g5->payByCustomer($this->transaction, $user->g5_id) : true;
            if($response){
                $this->transaction->update(['is_user_credited' => true]);
                $wallet->balance += $this->transaction->amount;
                $wallet->save();

                $user->name = $user->firstname;
                Mail::to($user)->send(new CreditNotificationMail($user->name, $user->account_number, $this->transaction->amount, $wallet->balance));
            }
        }
    }
}
