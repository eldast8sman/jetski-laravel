<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Services\SparkleService;

class WalletRepository extends AbstractRepository implements WalletRepositoryInterface
{
    public $errors;

    public function __construct(Wallet $wallet)
    {
        parent::__construct($wallet);
    }
    
    public function wallet_transactions($user_id, $type="", $from="", $to="", $sort="desc", $limit=10){
        $wallet = $this->findFirstBy(['user_id' => $user_id]);

        parent::__construct(new WalletTransaction());

        $data = [
            ['wallet_id', '=', $wallet->id]
        ];
        if(!empty($type)){
            $data[] = ['type', '=', $type];
        }
        if(!empty($from)){
            $data[] = ['created_at', '>=', $from.' 00:00:00'];
        }
        if(!empty($to)){
            $data[] = ['created_at', '<=', $to.' 23:59:59'];
        }

        $orderBy = [
            ['created_at', $sort]
        ];

        $transactions = $this->findBy($data, $orderBy, $limit);
        return $transactions;
    }

    public function userTransactions(int $id, int $limit=10)
    {
        $wallet = $this->findFirstBy([
            'user_id' => $id
        ]);

        $transactions = $wallet->transactions()->orderBy('id', 'desc')->paginate($limit);
        return $transactions;
    }

    public function userTransaction(string $uuid, int $user_id)
    {
        $wallet = $this->findFirstBy([
            'user_id' => $user_id
        ]);

        $transaction = $wallet->transactions()->where('uuid', $uuid)->first();
        if(empty($transaction)){
            $this->errors = "No Transaction fetched";
            return false;
        }
        
        return $transaction;
    }

    public function fetch_wallet(User $user)
    {
        if(empty($user->account_number)){
            $sparkle = new SparkleService();
            $reference = "SPK_Jetski_".$user->uuid;
            $payload = [
                "name" => "{$user->firstname} {$user->lastname}",
                "external_reference" =>  $reference,
                "email" => $user->email,
                "expires_at" => "2027-12-31 23:59:59",
                "bank_verification_number" => "01234567891",
                "is_permanent" => 0,
                "is_active" => 1
              ];

            $account = $sparkle->createAccount($payload);
            if($account){
                $details = $account['data']['account'];
                $account_number = $details['account_number'];
                $sparkle_id = $details['id'];
                $user->update([
                    'account_number' => $account_number,
                    'sparkle_id' => $sparkle_id,
                    'external_sparkle_reference' => $reference
                ]);
            }
        }

        $wallet = $this->findFirstBy(['user_id' => $user->id]);
        return $wallet;
    }
}