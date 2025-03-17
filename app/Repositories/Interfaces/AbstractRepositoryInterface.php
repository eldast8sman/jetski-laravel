<?php

namespace App\Repositories\Interfaces;

interface AbstractRepositoryInterface
{
    public function all($orderBy=[], $limit=null, $count=false);

    public function find(int $id);

    public function findByUuid($uuid);

    public function findBy(array $array);

    public function findFirstBy(array $array);

    public function update($id, $data=[]);
}
