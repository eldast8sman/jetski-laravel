<?php

namespace App\Repositories;

use App\Models\MenuCategory;
use App\Repositories\Interfaces\MenuCategoryRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuCategoryRepository extends AbstractRepository implements MenuCategoryRepositoryInterface
{
    public $errors = "";

    public function __construct(MenuCategory $category)
    {
        parent::__construct($category);
    }

    public function store(Request $request){
        try {
            $all = $request->all();
            $all['uuid'] = Str::uuid().'-'.time();

            $category = $this->create($all);

            return $category;
        } catch (Exception $e) {
            $this->errors = $e->getMessage();
            return false;
        }
    }

    public function index()
    {
        $categories = $this->all();
        return $categories;
    }

    public function show($uuid){
        return $this->findByUuid($uuid);
    }

    public function update_category(Request $request, string $uuid)
    {
        $category = $this->findByUuid($uuid);

        $category->update($request->all());
        return $category;
    }

    public function delete_category($uuid)
    {
        $category = $this->findByUuid($uuid);
        $this->delete($category);
        return true;   
    }
}