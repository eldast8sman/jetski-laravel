<?php

namespace App\Jobs;

use App\Mail\CreditNotificationMail;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\G5PosService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SaveSparkleTransactionJob implements ShouldQueue
{
    use Queueable;

    private $transaction;
    private $g5;

    /**
     * Create a new job instance.
     */
    public function __construct(array $transaction)
    {
        $this->transaction = $transaction;
        $this->g5  = new G5PosService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $transaction = $this->transaction;
        $account = $transaction['account'];
        if(empty($user = User::where('external_sparkle_reference', $account['external_reference'])->first())){
            Log::info("User not found for account: ".json_encode($account));
            return;
        }
        $wallet = $user->wallet;
        if(!empty($trans = WalletTransaction::where('external_reference', $transaction['external_reference'])->where('type', 'Credit')->first())){
            return;
        }
        $trans = WalletTransaction::create([
            'uuid' => Str::uuid().'-'.time(),
            'wallet_id' => $wallet->id,
            'amount' => $transaction['amount'],
            'type' => 'Credit',
            'is_user_credited' => false,
            'payment_processor' => 'SPARKLE',
            'external_reference' => $transaction['external_reference']
        ]);
        if(!empty($user->g5_id)){
            // $response = (env('APP_ENV') == 'production') ? $this->g5->payByCustomer($trans, $user->g5_id) : true;
            $response = $this->g5->payByCustomer($trans, $user->g5_id);
            if($response){
                $trans->update(['is_user_credited' => true]);
                $wallet->balance += $transaction['amount'];
                $wallet->save();

                $user->name = $user->firstname;
                Mail::to($user)->send(new CreditNotificationMail($user->name, $user->account_number, $transaction['amount'], $wallet->balance));
            }
        }
    }
}
