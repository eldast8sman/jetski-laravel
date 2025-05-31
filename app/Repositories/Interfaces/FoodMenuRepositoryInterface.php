<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface FoodMenuRepositoryInterface extends AbstractRepositoryInterface
{
    public function index($screen_uuid=1, $limit=10, $search="");

    public function user_index($screen_slug=1, $limit=10, $search="");

    public function deleted_index($screen_uuid=1, $limit=10, $search="");

    public function new_menu($screen_uuid=1,$limit=10, $search="");

    public function show(string $identifier);

    public function update_menu(string $uuid, Request $request);

    public function availability(string $uuid);

    public function is_delete(string $uuid);

    public function delete_photo(string $uuid);

    public function fetch_add_ons(string $search="");

    public function delivery_fees($limit=10, $search="");

    public function track_screen($ref, $screen_id) : bool;
}