<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrderRequest;
use App\Http\Resources\Admin\OrderDetailResource;
use App\Models\User;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Exception;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $order;
    protected $user;

    public function __construct(OrderRepositoryInterface $order, MemberRepositoryInterface $user)
    {
        $this->order = $order;
        $this->user = $user;
    }

    public function index(){
        $limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 4;

        $orders = $this->order->pending_orders($limit);

        return $this->success_response("Orders fetched successfully", OrderDetailResource::collection($orders)->response()->getData(true));
    }

    public function past_orders(){
        $limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 15;
        $orders = $this->order->all_past_orders($limit);
        return $this->success_response("Orders fetched successfully", OrderDetailResource::collection($orders)->response()->getData(true));
    }

    public function search(Request $request){
        $limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 15;
        $orders = $this->order->admin_search($request->order_no, $limit);
        return $this->success_response("Orders fetched successfully", OrderDetailResource::collection($orders)->response()->getData(true));
    }

    public function show($id){
        $order = $this->order->find($id);
        if(empty($order)){
            return $this->failed_response("No Order was fetched", 404);
        }
        return $this->success_response("Order fetched successfully", new OrderDetailResource($order));
    }

    public function store(StoreOrderRequest $request){
        $data = $request->all();
        $user = $this->user->find($data['user_id']);
        $data['g5_id'] = $user->g5_id;
        $data['category'] = 'Meal';

        try {
            if(!$order = $this->order->make_order($data)){
                return $this->failed_response($this->order->errors, 400);
            }

            return $this->success_response("Order added successfully", new OrderDetailResource($order));
        } catch(Exception $e){
            return $this->failed_response($e->getMessage());
        }
    }
}
