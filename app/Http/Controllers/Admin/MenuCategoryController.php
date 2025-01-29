<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMenuCategoryRequest;
use App\Http\Resources\Admin\MenuCategoryResource;
use App\Repositories\Interfaces\MenuCategoryRepositoryInterface;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    private $category;
    
    public function __construct(MenuCategoryRepositoryInterface $category)
    {
        $this->category = $category;
    }

    public function index(){
        return $this->success_response('Menu Categories fetched successfully', MenuCategoryResource::collection($this->category->index()));
    }

    public function store(StoreMenuCategoryRequest $request){
        if(!$category = $this->category->store($request)){
            return $this->failed_response($this->category->errors);
        }

        return $this->success_response('Category added successfully', new MenuCategoryResource($category));
    }

    public function show($uuid){
        if(empty($category = $this->category->show($uuid))){
            return $this->failed_response('No Category was fetched', 404);
        }

        return $this->success_response('Category fetched successfully', new MenuCategoryResource($category));
    }

    public function update(StoreMenuCategoryRequest $request, $uuid){
        if(!$category = $this->category->update_category($request, $uuid)){
            return $this->failed_response('Update Failed', 500);
        }

        return $this->success_response('Category Updated successfully', new MenuCategoryResource($category));
    }

    public function destroy($uuid){
        if(!$this->category->delete_category($uuid)){
            return $this->failed_response('Failed to delete');
        }

        return $this->success_response('Category deleted successfully');
    }
}
