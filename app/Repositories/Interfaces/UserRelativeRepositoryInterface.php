<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface UserRelativeRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(Request $request);

    public function getRelatives();

    public function getRelative($id);

    public function updateRelative(Request $request, string $id);

    public function user_activation(Request $request, $uuid);

    public function deleteRelative(string $id);
}