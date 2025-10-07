<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangeOrderStatusRequest;
use App\Http\Requests\Admin\ModifyOrderRequest;
use App\Http\Requests\Admin\PlaceOrderRequest;
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

    public function offline_orders(Request $request){
        $limit = $request->has('limit') ? (int)$request->limit : 10;

        $orders = $this->order->offline_orders($limit);
        dd($orders);

        return $this->success_response("Offline Orders fetched successfully", OrderCartResource::collection($orders)->response()->getData(true));
    }

    public function show($uuid){
        $order = $this->order->show($uuid);
        if(empty($order)){
            return $this->failed_response("No Order was fetched", 404);
        }
        return $this->success_response("Order fetched successfully", new OrderCartResource($order));
    }

    public function place_order(PlaceOrderRequest $request){
        if(!$place = $this->order->admin_place_order($request, auth('admin-api')->user())){
            return $this->failed_response($this->order->errors, 400);
        }

        return $this->success_response("Order placed successfully", new OrderCartResource($place));
    }

    public function modify_order(ModifyOrderRequest $request, $uuid){
        if(!$modify = $this->order->modify_order($uuid, $request)){
            return $this->failed_response($this->order->errors, 400);
        }

        return $this->success_response("Order Modified successfully", new OrderCartResource($modify));
    }

    public function confirm_order(Request $request, $uuid){
        if(!$confirm = $this->order->confirm_order($request, $uuid)){
            return $this->failed_response($this->order->errors, 400);
        }

        return $this->success_response("Order Confirmed successfully", new OrderCartResource($confirm));
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
