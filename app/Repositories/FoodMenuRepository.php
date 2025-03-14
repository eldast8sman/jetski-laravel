<?php

namespace App\Repositories;

use App\Models\FoodMenu;
use App\Models\FoodMenuPhoto;
use App\Models\MenuCategory;
use App\Models\MenuScreenTracker;
use App\Repositories\Interfaces\FoodMenuRepositoryInterface;
use App\Services\FileManagerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FoodMenuRepository extends AbstractRepository implements FoodMenuRepositoryInterface
{
    public $errors;

    public function __construct(FoodMenu $menu)
    {
        parent::__construct($menu);
    }

    public function index($screen_uuid=1, $limit=10, $search="")
    {
        if($screen_uuid != 1){
            $screen = $this->findByUuid($screen_uuid);
            if(empty($screen) or ($screen->type != 'screen')){
                $this->errors ="Wrong screen";
                return false;
            }
            $screen_id = $screen->g5_id;
        } else {
            $screen_id = 1;
        }
        $criteria = [
            ['is_stand_alone', '=', 1],
            ['parent_id', '=', $screen_id]
        ];

        if(!empty($search)){
            $criteria[] = ['name', 'like', '%'.$search.'%'];
        }
        $orderBy = [
            ['name', 'asc']
        ];
        $menus = $this->findBy($criteria, $orderBy, $limit);
        return $menus;
    }

    public function user_index($screen_uuid=1, $limit = 10, $search = "")
    {
        $menu = FoodMenu::isValid();
        if(!empty($search)){
            $menu = $menu->where('name', 'like', '%'.$search.'%');
        }
        if($screen_uuid == 1){
            $screen_id=1;
        } else {
            $screen = $this->findByUuid($screen_uuid);
            if(empty($screen) or ($screen->type != 'screen')){
                $this->errors ="Wrong screen";
                return false;
            }
            $screen_id = $screen->g5_id;
        }

        $menu = $menu->where('parent_id', $screen_id);

        return $menu->paginate($limit);
    }

    public function new_menu($screen_uuid=1, $limit=10, $search="")
    {
        if($screen_uuid != 1){
            $screen = $this->findByUuid($screen_uuid);
            if(empty($screen) or ($screen->type != 'screen')){
                $this->errors ="Wrong screen";
                return false;
            }
            $screen_id = $screen->g5_id;
        } else {
            $screen_id = 1;
        }

        $data = [
            ['is_new', '=', 1],
            ['parent_id', '=', $screen_id]
        ];
        if(!empty($search)){
            $data[] = ['name', 'like', '%'.$search.'%'];
        }

        $orderBy = [
            ['name', 'asc']
        ];
        $menus = $this->findBy($data, $orderBy, $limit);
        return $menus;
    }

    public function fetch_add_ons(string $search="")
    {
        $data = [
            ['is_add_on', '=', 1]
        ];
        if(!empty($search)){
            $data[] = ['name', 'like', '%'.$search.'%'];
        }

        return $this->findBy($data, [['name', 'asc']]);
    }

    public function show(string $identifier)
    {
        $criteria = [
            ['uuid' => $identifier],
            ['slug' => $identifier]
        ];

        $menu = $this->findByOrFirst($criteria);
        if($menu->type != 'item'){
            $this->errors = "Not an Item";
            return false;
        }

        return $menu;
    }

    public function update_menu(string $uuid, Request $request)
    {
        $menu = $this->findFirstBy(['uuid' => $uuid]);
        if(empty($menu)){
            $this->errors = "No Menu found";
            return false;
        }

        $data = $request->except(['photos', 'menu_category', 'add_ons']);

        $add_ons = [];
        if(isset($request->add_ons) and !empty($request->add_ons)){
            foreach($request->add_ons as $uuid){
                $add_on = $this->findByUuid($uuid);
                $add_ons[] = $add_on->id;
            }
        }
        $data['add_ons'] = json_encode($add_ons);

        if(!empty($request->menu_category)){
            $category = MenuCategory::where('uuid', $request->menu_category)->first();
            if(!empty($category)){
                $data['menu_category_id'] = $category->id;
            }
        } else {
            $data['menu_category_id'] = null;
        }

        if($menu->type == 'item'){
            $data['is_new'] = 0;
        }

        $menu = $this->update($menu->id, $data);

        if(isset($request->photos) and !empty($request->photos)){
            foreach($request->photos as $photo){
                $upload = FileManagerService::upload_file($photo, env('FILESYSTEM_DISK'));
                if($upload){
                    FoodMenuPhoto::create([
                        'uuid' => Str::uuid().'-'.time(),
                        'food_menu_id' => $menu->id,
                        'file_manager_id' => $upload->id
                    ]);
                }
            }
        }


        return $menu;
    }

    public function availability(string $uuid)
    {
        $menu = $this->findFirstBy(['uuid' => $uuid]);
        $data = [
            'availability' => ($menu->availability == 0) ? 1 : 0
        ];
        $menu = $this->update($menu->id, $data);

        return $menu;
    }

    public function delete_photo(string $uuid)
    {
        parent::__construct(new FoodMenuPhoto());
        $photo = $this->findFirstBy(['uuid' => $uuid]);
        $photo->delete();
        $file = new FileManagerService();
        $file->delete($photo->file_manager_id);

        return true;
    }

    public function delivery_fees($limit = 10, $search="")
    {
        $fees = FoodMenu::where('is_delivery_fee', 1);
        if(!empty($search)){
            $fees = $fees->where(function($query) use ($search){
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        return $fees->paginate($limit);
    }

    public function track_screen($ref, $screen_id) : bool
    {
        if(empty(MenuScreenTracker::where('ref', $ref)->where('screen_id', $screen_id)->first())){
            MenuScreenTracker::create(['ref' => $ref, 'screen_id' => $screen_id]);
            return true;
        } else {
            return false;
        }
    }
}