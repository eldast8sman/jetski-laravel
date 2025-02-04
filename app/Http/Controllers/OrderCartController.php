<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddItemToCartRequest;
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

    public function add_item_to_cart(AddItemToCartRequest $request){
        $all = $request->all();
        $all['identifier'] = $all['slug'];
        unset($all['slug']);
        $all['user_id'] = auth('user-api')->user()->id;

        $add = $this->items->store($all);
        if(!$add){
            return $this->failed_response($this->items->errors, 400);
        }

        return $this->success_response("Order successfully placed", new OrderCartItemResource($add));
    }

    public function update_item(Request $request, $uuid){
        $all = $request->all();

        $update = $this->items->update($uuid, $all);
        if(!$update){
            return $this->failed_response($this->items->errors, 400);
        }

        return $this->success_response("Order successfully updated", new OrderCartItemResource($update));
    }

    public function remove($uuid){
        $remove = $this->items->remove_item($uuid);
        if(!$remove){
            return $this->failed_response($this->items->errors, 400);
        }

        return $this->success_response("Order successfully removed", new OrderCartItemResource($remove));
    }

    public function show($uuid){
        $cart = $this->items->findBy([
            'uuid' => $uuid,
            'user_id' => auth('user-api')->user()->id
        ]);
        if(empty($cart)){
            return $this->failed_response("No Order was fetched", 404);
        }

        return $this->success_response("Order Cart fetched successfully", new OrderCartResource($cart));
    }

    public function place_order($uuid){
        $place = $this->order->user_place_order($uuid);
        if(!$place){
            return $this->failed_response($this->order->errors, 400);
        }

        return $this->success_response("Order successfully placed", new OrderCartResource($place));
    }
}
