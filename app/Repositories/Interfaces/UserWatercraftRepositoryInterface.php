<?php

namespace App\Repositories\Interfaces;

interface UserWatercraftRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(array $data, int $user_id);
}