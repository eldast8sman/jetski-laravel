<?php

namespace App\Services;

use App\Mail\CreditNotificationMail;
use App\Models\SparkleWebhook;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SparkleService
{
    private $base_url;
    private $token;

    public function __construct()
    {
        $this->base_url = config('sparkle.api_credentials.base_url');
        $this->token = Cache::remember('sparkle_token', 60, function(){
            $result = $this->login();
            return $result['data']['access_token'];
        });
    }

    public function login(){
        $url = config('sparkle.api_credentials.base_url')."/auth";
        $data = [
            'email' => config('sparkle.api_credentials.email'),
            'password' => config('sparkle.api_credentials.password')
        ];
        $response = Http::withBasicAuth(config('sparkle.api_credentials.client_key'), config('sparkle.api_credentials.secret_key'))->post($url, $data);
        return $this->responseHandler($response);

    }

    public function createCustomer(array $data){
        $url = $this->base_url."/customer/create";
        $response = Http::withToken($this->token)->post($url, $data);

        return $this->responseHandler($response);
    }

    public function createAccount(array $data){
        $url = $this->base_url."/account/create-account";
        
        $response = Http::withToken($this->token)->post($url, $data);
        return $this->responseHandler($response);
    }

    public function getCustomers(){
        $url = $this->base_url."/customers";
        $response = Http::withToken($this->token)->get($url);
        return $this->responseHandler($response);
    }
    
    public function transactions(){
        
    }

    public function getCustomerAccountwithId(int $id)
    {
        $url = $this->base_url."/fetch-accounts/$id";

        $response = Http::withToken($this->token)->get($url);

        return $this->responseHandler($response);
    }

    public function getTransactions($start_date="", $end_date="")
    {
        $url = $this->base_url."/transactions";
        $params = [];
        if(!empty($start_date)){
            $params['start_date'] = $start_date;
        }
        if(!empty($end_date)){
            $params['end_date'] = $end_date;
        }
        
        if(!empty($params)){
            $url = $url.'?'.http_build_query($params);
        }

        $transactions = Http::withToken($this->token)->get($url);
        return $this->responseHandler($transactions);
    }

    public function responseHandler(Response $response){
        if ($response->failed()) {
            Log::error($response->throw()->json());
            return false;
        }

        return $response->json();
    }

    public function webhook($data, G5PosService $g5){
        $webhook = SparkleWebhook::create([
            'data' => json_encode($data),
        ]);
        if($data['event'] != "transaction.credit.successful"){
            return false;
        }
        if(!isset($data['beneficiary_account_number']) or empty($data['beneficiary_account_number'])){
            return false;
        }

        if(empty($user = User::where('account_number', $data['beneficiary_account_number'])->first())){
            return false;
        }

        $webhook->user_id = $user->id;
        $webhook->user_name = $user->firstname.' '.$user->lastname;
        $webhook->amount = $data['amount'];
        $webhook->save();

        $wallet = $user->wallet()->first;

        if(empty($trans = WalletTransaction::where('external_reference', $data['external_reference'])->first())){
            $trans = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $data['amount'],
                'type' => 'Credit',
                'is_user_credited' => false,
                'payment_processor' => 'SPARKLE',
                'external_reference' => $data['external_reference']
            ]);

            if(empty($user->g5_id)){
                return false;
            }

            if(!$response = $g5->payByCustomer($trans, $user->g5_id)){
                return false;
            }

            $wallet->balance += $data['amount'];
            $wallet->save();
            $trans->update(['is_user_credited' => true]);
            $webhook->g5_response = $response;
            $webhook->save();

            $user->name = $user->firstname;
            Mail::to($user)->send(new CreditNotificationMail($user->name, $user->account_number, $data['amount'], $wallet->balance));
        } elseif($trans->is_user_credited == false){
            if(empty($user->g5_id)){
                return false;
            }
            if(!$response = $g5->payByCustomer($trans, $user->g5_id)){
                return false;
            }

            $wallet->balance += $data['amount'];
            $wallet->save();
            $trans->update(['is_user_credited' => true]);
            $webhook->g5_response = $response;
            $webhook->save();
        }
    }
}