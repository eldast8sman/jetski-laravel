<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateFoodMenuRequest;
use App\Http\Resources\Admin\AllFoodMenuResource;
use App\Http\Resources\Admin\DeliveryFeeResource;
use App\Http\Resources\Admin\MenuAddOnResource;
use App\Http\Resources\Admin\SingleFoodMenuResource;
use App\Jobs\StoreFoodMenuJob;
use App\Repositories\Interfaces\FoodMenuRepositoryInterface;
use App\Repositories\Interfaces\MenuCategoryRepositoryInterface;
use App\Services\G5PosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FoodMenuController extends Controller
{
    protected $menu;
    protected $category;

    public function __construct(FoodMenuRepositoryInterface $menu, MenuCategoryRepositoryInterface $category)
    {
        $this->menu = $menu;
        $this->category = $category;
    }

    public function refresh_menu(){
        try {
            $service = new G5PosService();

            $menus = $service->getMenu([
                'ScreenID' => 1,
                'Type' => 3
            ]);

            $menus = json_decode($menus, true);

            return $menus;

            // $ref = Str::random(20).time();
            // $this->menu->track_screen($ref, 1);
            // foreach($menus as $menu){
            //     StoreFoodMenuJob::dispatch($menu, 1, $ref);
            // }

            // return $this->success_response('Menu refreshed successfully');
        } catch(\Exception $e){
            Log::error('Store G5 Menu: '.$e->getMessage());
            return $this->failed_response('Refresh failed. Check Logs for details');
        }
    }

    public function index(Request $request, $screen_uuid=1){
        $limit = $request->has('limit') ? (int)$request->limit : 10;
        $search = $request->has('search') ? (string)$request->search: "";
        $menus = $this->menu->index($screen_uuid, $limit, $search);
        if(!$menus){
            return $this->failed_response($this->menu->errors);
        }
        return $this->success_response('Food Menu fetched successfully', AllFoodMenuResource::collection($menus)->response()->getData(true));
    }

    public function new_menu(Request $request){
        $limit = $request->has('limit') ? (int)$request->limit : 9;
        $search = $request->has('search') ? $request->search : "";
        $menus = $this->menu->new_menu($limit, $search);
        return $this->success_response("Food Menu fetched successfully", AllFoodMenuResource::collection($menus)->response()->getData(true));
    }

    public function add_ons(Request $request){
        $search = $request->has('search') ? $request->search : "";
        $add_ons = $this->menu->fetch_add_ons($search);
        return $this->success_response("Add Ons fetched successfull", MenuAddOnResource::collection($add_ons));
    }

    public function show($uuid){
        if(empty($menu = $this->menu->show($uuid))){
            return $this->failed_response('No Menu was fetched', 404);
        }

        return $this->success_response('Menu successfully fetched', new SingleFoodMenuResource($menu));
    }

    public function availability($uuid){
        $menu = $this->menu->availability($uuid);

        return $this->success_response('Operation successful', new SingleFoodMenuResource($menu));
    }

    public function update(UpdateFoodMenuRequest $request, $uuid){
        if(!$menu = $this->menu->update_menu($uuid, $request)){
            return $this->failed_response($this->menu->errors, 400);
        }

        return $this->success_response('Food Menu updated succesfully', new SingleFoodMenuResource($menu));
    }

    public function delete_photo(string $uuid){
        $this->menu->delete_photo($uuid);

        return $this->success_response('Photo successfully deleted');
    }

    public function delivery_fees(Request $request){
        $search = $request->has('search') ? (string)$request->search : "";
        $limit = $request->has('limit') ? (int)$request->limit : 10;

        $fees = $this->menu->delivery_fees($limit, $search);
        return $this->success_response('Delivery Fees fetched successfully', DeliveryFeeResource::collection($fees)->response()->getData(true));
    }
}
