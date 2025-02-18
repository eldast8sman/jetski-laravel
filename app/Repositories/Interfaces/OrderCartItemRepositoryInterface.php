<?php

namespace App\Repositories\Interfaces;

interface OrderCartItemRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(array $data);
}