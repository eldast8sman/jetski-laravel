<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Http\Request;

interface MemberRepositoryInterface extends AbstractRepositoryInterface
{
    public function fetch_g5_customers();

    public function store(array $data, $balance=null);

    public function store_user(Request $request);

    public function index($limit);

    public function all_members($limit=null);

    public function resend_activation_link(User $user);

    public function update_user(User $user, array $data);

    public function update_member(Request $request, User $user);

    public function user_activation(Request $request, $uuid);

    public function fetch_member_by_param($key, $value);
}