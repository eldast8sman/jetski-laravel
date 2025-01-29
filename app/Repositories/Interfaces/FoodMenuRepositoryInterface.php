<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface FoodMenuRepositoryInterface extends AbstractRepositoryInterface
{
    public function index($limit=10, $category_id=null, $search="");

    public function user_index($limit=10, $category_id=null, $search="");

    public function new_menu($limit=10, $search="");

    public function show(string $identifier);

    public function update_menu(string $uuid, Request $request);

    public function availability(string $uuid);

    public function delete_photo(string $uuid);

    public function fetch_add_ons(string $search="");
}