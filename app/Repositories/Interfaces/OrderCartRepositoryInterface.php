<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Http\Request;

interface OrderCartRepositoryInterface extends AbstractRepositoryInterface
{
    public function admin_place_order($uuid, User $user);

    public function user_place_order(Request $request, $uuid);

    public function index($limit=10, $search="");

    public function completed_orders($limit=10, $search="");

    public function user_index($limit=10);

    public function user_completed_orders($limit=10);

    public function show($uuid);

    public function change_status($uuid, $status);
}