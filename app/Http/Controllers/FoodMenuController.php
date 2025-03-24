<?php

namespace App\Http\Controllers;

use App\Http\Resources\AllFoodMenuResource;
use App\Http\Resources\MenuCategoryResource;
use App\Http\Resources\SingleFoodMenuResource;
use App\Repositories\Interfaces\FoodMenuRepositoryInterface;
use App\Repositories\Interfaces\MenuCategoryRepositoryInterface;
use Illuminate\Http\Request;

class FoodMenuController extends Controller
{
    protected $menu;
    protected $category;

    public function __construct(FoodMenuRepositoryInterface $menu, MenuCategoryRepositoryInterface $category)
    {
        $this->menu = $menu;
        $this->category = $category;
    }

    public function index(Request $request, $slug=1){
        $limit = $request->has('limit') ? (int)$request->limit : 9;
        $search = $request->has('search') ? (string)$request->search: "";

        $menus = $this->menu->user_index($slug, $limit, $search);
        if(!$menus){
            return $this->failed_response($this->menu->errors, 500);
        }
        return $this->success_response('Menu fetched successfully', AllFoodMenuResource::collection($menus)->response()->getData(true));
    }

    public function show($slug){
        $menu = $this->menu->show($slug);
        if(!$menu){
            return $this->failed_response('Wrong Link', 404);
        }

        return $this->success_response('Menu fetched successfully', new SingleFoodMenuResource($menu));
    }
}
