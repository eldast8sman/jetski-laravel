<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface InviteRepositoryInterface extends AbstractRepositoryInterface
{
    public function booking(string $uuid);

    public function store(Request $request, string $uuid);
}