<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangeOrderStatusRequest;
use App\Http\Resources\Admin\AllOrderCartResource;
use App\Http\Resources\Admin\OrderCartResource;
use App\Repositories\Interfaces\OrderCartItemRepositoryInterface;
use App\Repositories\Interfaces\OrderCartRepositoryInterface;
use Illuminate\Http\Request;

class OrderCartController extends Controller
{
    public $order;
    public $items;

    public function __construct(OrderCartItemRepositoryInterface $items, OrderCartRepositoryInterface $order)
    {
        $this->items = $items;
        $this->order = $order;
    }

    public function index(Request $request){
        $limit = $request->has('limit') ? (int)$request->limit : 10;

        $orders = $this->order->index($limit);

        return $this->success_response("Orders fetched successfully", OrderCartResource::collection($orders)->response()->getData(true));
    }

    public function completed_orders(Request $request){
        $limit = $request->has('limit') ? (int)$request->limit : 10;

        $orders = $this->order->completed_orders($limit);

        return $this->success_response("Completed Orders fetched successfully", OrderCartResource::collection($orders)->response()->getData(true));
    }

    public function show($uuid){
        $order = $this->order->show($uuid);
        if(empty($order)){
            return $this->failed_response("No Order was fetched", 404);
        }
        return $this->success_response("Order fetched successfully", new OrderCartResource($order));
    }

    public function change_status(ChangeOrderStatusRequest $request, $uuid){
        $status = $request->status;
        $updated = $this->order->change_status($uuid, $status);
        if(!$updated){
            return $this->failed_response($this->order->errors, 400);
        } 
        
        return $this->success_response("Order status updated successfully", new OrderCartResource($updated));
    }
}
