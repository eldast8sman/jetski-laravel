<?php

namespace App\Repositories;

use App\Models\FoodMenu;
use App\Models\OrderCartItem;
use App\Repositories\Interfaces\OrderCartItemRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OrderCartItemRepository extends AbstractRepository implements OrderCartItemRepositoryInterface
{
    public $errors = "";
    public $menu;

    public function __construct(OrderCartItem $item)
    {
        parent::__construct($item);
        $this->menu = new FoodMenuRepository(new FoodMenu());
    }

    public function store(array $data)
    {
        try {
            $item = $this->menu->show($data['id']);
            if(empty($item)){
                $this->errors = "Item not found";
                return false;
            }
            if($item->is_stand_alone != 1){
                $this->errors = "Item not Stand Alone for Ordering";
                return false;
            }
            if(!$this->check_availability($item)){
                $this->errors = "Item not available";
                return false;
            }

            $add_ons = null;
            $add_on_price = 0;
            if(isset($data['add_ons']) and !empty($data['add_ons'])){
                $sorted = $this->sort_add_ons($data['add_ons'], $item);
                $add_ons = $sorted['add_ons'];
                $add_on_price = $sorted['total_price'];
            }
            
            $total_unit_price = $item->amount + $add_on_price;
            $total_price = $total_unit_price * $data['quantity'];
            $data = [
                'uuid' => Str::uuid().'-'.time(),
                'user_id' => $data['user_id'],
                'food_menu_id' => $item->id,
                'add_ons' => $add_ons,
                'add_on_price' => $add_on_price,
                'unit_price' => $item->amount,
                'total_unit_price' => $total_unit_price,
                'quantity' => $data['quantity'],
                'total_price' => $total_price
            ];
            
            $cart_item = $this->create($data);

            return $cart_item;
        } catch(\Exception $e){
            $this->errors = $e->getMessage();
            return false;
        }
    }

    public function remove_item($uuid)
    {
        try {
            $cart_item = $this->findByUuid($uuid);
            $cart_item->delete();
            return true;
        } catch(\Exception $e){
            $this->errors = $e->getMessage();
            return false;
        }
    }

    public function sort_add_ons(array $add_ons, FoodMenu $item){
        $addOns = [];
        $total_price = 0;

        $item_add_ons = json_decode($item->add_ons, true);
        foreach($add_ons as $add_on){
            $item = $this->menu->show($add_on['id']);
            if($item->is_add_on != 1){
                continue;
            }
            if((in_array($item->id, $item_add_ons)) and ($this->check_availability($item))){
                $addOns[] = [
                    'id' => $item->id,
                    'quantity' => $add_on['quantity'],
                    'unit_price' => $item->amount,
                    'total_price' => $add_on['quantity'] * $item->amount
                ];
                $total_price += $add_on['quantity'] * $item->amount;
            }
        }

        return [
            'add_ons' => !empty($addOns) ? json_encode($addOns) : null,
            'total_price' => $total_price
        ];
    }

    public function sort_modifier($modifier, FoodMenu $item){
        $modifier = $this->menu->show($modifier);
        if($modifier->group_id != $item->modifier_id){
            return false;
        }

        return $modifier;
    }

    private function check_availability(FoodMenu $item) : bool
    {
        $now = Carbon::now('Africa/Lagos');

        $today = $now->format('l');

        if($item->availability != 1){
            return false;
        }
        if(empty($item->availability_time)){
            $availability = true;
        } else {
            $availability = false;
            $availability_times = json_decode($item->availability_time, true);
            foreach($availability_times as $availability_time){
                if($availability_time['day'] == $today){
                    if($availability_time['status'] == 'Opened'){
                        $start = Carbon::parse($availability_time['from']);
                        $end = Carbon::parse($availability_time['to']);
                        if($now->between($start, $end)){
                            $availability = true;
                            break;
                        }
                    }
                }
            }
        }

        return $availability;
    }
}
