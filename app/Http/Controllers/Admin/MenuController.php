<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\MenuItemResource;
use App\Http\Resources\Admin\ModifierResource;
use App\Repositories\Interfaces\MenuRepositoryInterface;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    protected $menu;

    public function __construct(MenuRepositoryInterface $menu)
    {
        $this->menu = $menu;
    }

    public function store_g5_menu(){
        $menus = $this->menu->fetch_g5_menu();
        if(!$menus){
            return $this->failed_response($this->menu->errors);
        }

        return $this->success_response("menus Fetched", $menus);
    }

    public function index(){
        $limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 9;
        $menus = $this->menu->index($limit);

        return $this->success_response("Menu Items fetched successfully", MenuItemResource::collection($menus)->response()->getData(true));
    }

    public function show(int $id){
        $menus = $this->menu->fetch_menu($id);

        return $this->success_response("Menu Item fetched successfully", $menus);
    }

    public function search(Request $request){
        $limit = !empty($_GET['limit']) ? (int)$_GET['limit'] : 9;
        $menus = $this->menu->fetchByName($request, $limit);

        return $this->success_response("Menu Items fetched successfully", MenuItemResource::collection($menus)->response()->getData(true));
    }

    public function modifiers($id){
        $modifiers = $this->menu->getModifiers($id);
        if(!$modifiers){
            return $this->failed_response($this->menu->errors);
        }
        $modifiers = json_decode($modifiers);
        
        return $this->success_response("Modifiers fetched successfully", ModifierResource::collection($modifiers));
    }
}
