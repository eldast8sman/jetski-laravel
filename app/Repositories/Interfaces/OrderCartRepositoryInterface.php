<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface OrderCartRepositoryInterface extends AbstractRepositoryInterface
{
    public function admin_place_order($uuid, User $user);

    public function user_place_order($uuid);

    public function index($limit=10, $search="");

    public function user_index($limit=10);

    public function show($uuid);
}