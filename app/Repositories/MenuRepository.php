<?php

namespace App\Repositories;

use App\Jobs\StoreMenuJob;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Interfaces\MenuRepositoryInterface;
use App\Services\G5PosService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuRepository extends AbstractRepository implements MenuRepositoryInterface
{
    public $errors;

    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    public function fetch_g5_menu()
    {
        
        try {
            $service = new G5PosService();

            $menus = $service->getMenu([
                'ScreenID' => 1,
                'Type' => 3
            ]);

            $menus = json_decode($menus, true);
            foreach($menus as $menu){
                StoreMenuJob::dispatch($menu);
            }

            return $menus;
        } catch (Exception $e){
            $this->errors = $e->getMessage();
            return false;
        }
    }

    public function index(int $limit=9){
        $menus = $this->findBy(['parent_id' => 1], [], $limit);

        return $menus;
    }

    public function store(array $data){
        if(!empty($product = Product::where('screen_id', $data['WSScreenItemID'])->first())){
            return false;
        }
        
        $product = $this->create([
            'uuid' => Str::uuid().'-'.time(),
            'name' => ucfirst(strtolower($data['DisplayName'])),
            'description' => ucfirst(strtolower($data['Description'])),
            'amount' => $data['PriceMode1'],
            'available' => $data['Available'],
            'screen_id' => $data['WSScreenItemID'],
            'g5_id' => $data['ItemID'],
            'photo' => $data['Picture'] ?? "https://thumbs.dreamstime.com/b/no-thumbnail-image-placeholder-forums-blogs-websites-148010362.jpg",
            'parent_id' => $data['ParentID'],
            'modifier_id' => $data['Modifier1'],
            'group_id' => $data['GroupID']
        ]);

        return $product;
    }

    public function fetch_menu(int $page = 1)
    {
        $menus = Product::where('parent_id', $page);
        if(!empty($menus->count() > 0)){
            return [
                'type' => 'array',
                'menu' => $menus->get()
            ];
        }

        $menus = $this->findFirstBy(['g5_id' => $page]);
        if($menus->modifier_id != NULL){
            $modifiers = $this->findBy(['group_id' => $menus->modifier_id]);
        } else {
            $modifiers = [];
        }
        return [
            'type' => 'single',
            'menu' => $menus,
            'modifiers' => $modifiers
        ];
    }

    public function fetchByName(Request $request, int $limit=9)
    {
        return Product::where('name', 'like', '%'.$request->name.'%')->where('category', '!=', 'Infrastructure')->paginate($limit);
    }

    public function getModifiers($id)
    {
        try {
            $service = new G5PosService();
            $modifiers = $service->getModifiers(['ModifierID' => $id]);

            return $modifiers;
        } catch(Exception $e) {
            $this->errors = $e->getMessage();
            return false;
        }
    }

    public function membership_summary()
    {
        $data = [];
        $products = $this->findBy([
            'category' => 'Infrastructure'
        ], [
            ['name', 'asc']
        ], null);
        if(!empty($products)){
            parent::__construct(new User());
            foreach($products as $product){
                $members_count = $this->findBy(['membership_id' => $product->id], [], null, true);
                $data[] = [
                    'id' => $product->id,
                    'uuid' => $product->uuid,
                    'name' => $product->name,
                    'photo' => $product->photo,
                    'membership_count' => $members_count
                ];
            }
        }
        return $data;
    }
}