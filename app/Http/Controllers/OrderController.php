<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $order;
    protected $auth;

    public function __construct(OrderRepositoryInterface $order)
    {
        $this->order = $order;
        $this->auth = new AuthService();
    }

    public function index(){
        try {
            $orders = $this->order->index();

            return $this->success_response("Pending Orders fetched successfully", OrderResource::collection($orders)->response()->getData(true));
        } catch(Exception $e){
            return $this->failed_response($e->getMessage());
        }
    }

    public function past_orders(){
        $limit = !empty($_GET['limit']) ? $_GET['limit'] : 15;
        try {
            $orders = $this->order->past_orders($limit);

            return $this->success_response("Past Orders fetched successfully", OrderResource::collection($orders)->response()->getData(true));
        } catch(Exception $e){
            return $this->failed_response($e->getMessage());
        }
    }

    public function store(StoreOrderRequest $request){
        $user = $this->auth->logged_in_user();

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['g5_id'] = $user->g5_id;
        $data['category'] = 'Meal';

        try {
            if(!$order = $this->order->make_order($data)){
                return $this->failed_response($this->order->errors, 400);
            }

            return $this->success_response("Order successfully made", new OrderResource($order));
        } catch(Exception $e){
            return $this->failed_response($e->getMessage());
        }
    }
}
