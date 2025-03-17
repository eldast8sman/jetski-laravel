<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface MembershipTypeRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(array $request);
}