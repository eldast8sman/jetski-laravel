<?php

namespace App\Http\Controllers;

use App\Http\Resources\WalletDetailsResource;
use App\Http\Resources\WalletTransactionResource;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public $wallet;
    public $user;    

    public function __construct(WalletRepositoryInterface $wallet, UserRepositoryInterface $user)
    {
        $this->wallet = $wallet;
        $this->user = $user;
    }

    public function wallet_details(){
        $user = $this->user->findFirstBy(['id' => auth('user-api')->user()->id]);
        return $this->success_response("Wallet Details fetched successfully", new WalletDetailsResource($user));
    }

    public function wallet_transactions(Request $request){
        $type = $request->has('type') ? $request->type : "";
        $from = $request->has('from') ? $request->from : "";
        $to = $request->has('to') ? $request->to : "";
        $sort = $request->has('sort') ? $request->sort : "desc";
        $limit = $request->has('limit') ? $request->limit : 10;

        $transactions = $this->wallet->wallet_transactions(auth('user-api')->user()->id, $type, $from, $to, $sort, $limit);
        return $this->success_response("Wallet Transactions fetched successfully", WalletTransactionResource::collection($transactions)->response()->getData(true));
    }
}
