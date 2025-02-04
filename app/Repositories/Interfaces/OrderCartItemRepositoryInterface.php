<?php

namespace App\Repositories\Interfaces;

interface OrderCartItemRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(array $data);

    public function update_item(array $data, $uuid);

    public function remove_item($uuid);
}