<?php

namespace App\Repositories\Interfaces;

interface AbstractRepositoryInterface
{
    public function all();

    public function find(int $id);

    public function findByUuid($uuid);

    public function findBy(array $array);

    public function findFirstBy(array $array);

    public function update($id, $data=[]);
}
