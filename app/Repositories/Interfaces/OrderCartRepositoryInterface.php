<?php

namespace App\Repositories\Interfaces;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;

interface OrderCartRepositoryInterface extends AbstractRepositoryInterface
{
    public function admin_place_order(Request $request, Admin $admin);

    public function user_place_order(Request $request);

    public function index($limit=10, $search="");

    public function completed_orders($limit=10, $search="");

    public function offline_orders($limit=10, $search="");

    public function user_index($limit=10);

    public function user_offline_orders($limit=10);

    public function user_completed_orders($limit=10);

    public function show($uuid);

    public function modify_order(string $uuid, Request $request);

    public function change_status($uuid, $status);

    public function confirm_order(Request $request, string $uuid);
}