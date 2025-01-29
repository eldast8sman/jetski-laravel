<?php

namespace App\Http\Controllers;

use App\Http\Resources\WalletTransactionResource;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Services\AuthService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private $wallet;
    private $user;

    public function __construct(WalletRepositoryInterface $wallet)
    {
        $this->wallet = $wallet;
        $auth = new AuthService('user-api');
        $this->user = $auth->logged_in_user();
    }

    public function index(){
        $limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $payments = $this->wallet->userTransactions($this->user->id, $limit);  
        return $this->success_response("Transactions fetched successfully", WalletTransactionResource::collection($payments)->response()->getData(true));
    }

    public function show($uuid){
        $transaction = $this->wallet->userTransaction($uuid, $this->user->id);
        if(!$transaction){
            $this->failed_response($this->wallet->errors, 404);
        }

        return $this->success_response("Transaction fetched successfully", new WalletTransactionResource($transaction));
    }
}
