<?php

namespace App\Http\Controllers;

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

    public function index($id){
        $menus = $this->menu->fetch_menu($id);

        return $this->success_response("Menu Items fetched successfully", $menus);
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
