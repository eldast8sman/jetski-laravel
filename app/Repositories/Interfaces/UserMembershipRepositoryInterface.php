<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface UserMembershipRepositoryInterface extends AbstractRepositoryInterface
{
    public function membership_types();

    public function fetch_membership();

    public function update_membership(Request $request);
}