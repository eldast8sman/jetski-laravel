<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddItemToCartRequest;
use App\Http\Requests\PlaceOrderRequest;
use App\Http\Resources\OrderCartItemResource;
use App\Http\Resources\OrderCartResource;
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

        $orders = $this->order->user_index($limit);

        return $this->success_response("Orders fetched successfully", OrderCartResource::collection($orders)->response()->getData(true));
    }

    public function completed_orders(Request $request){
        $limit = $request->has('limit') ? (int)$request->limit : 10;

        $orders = $this->order->user_completed_orders($limit);

        return $this->success_response("Completed Orders fetched successfully", OrderCartResource::collection($orders)->response()->getData(true));
    }

    public function show($uuid){
        $cart = $this->order->findFirstBy([
            'uuid' => $uuid,
            'user_id' => auth('user-api')->user()->id
        ]);
        if(empty($cart)){
            return $this->failed_response("No Order was fetched", 404);
        }

        return $this->success_response("Order Cart fetched successfully", new OrderCartResource($cart));
    }

    public function place_order(PlaceOrderRequest $request){
        $place = $this->order->user_place_order($request);
        if(!$place){
            return $this->failed_response($this->order->errors, 400);
        }

        return $this->success_response("Order successfully placed", new OrderCartResource($place));
    }

    public function modify_order(PlaceOrderRequest $request, $uuid){
        $order = $this->order->findByUuid($uuid);
        if(empty($order) or ($order->user_id != auth('user-api')->user()->id)){
            return $this->failed_response("No Order was fetched", 404);
        }

        if(!$modify = $this->order->modify_order($uuid, $request)){
            return $this->failed_response($this->order->errors, 400);
        }

        return $this->success_response("Order modified successfully", new OrderCartResource($modify));
    }
}
