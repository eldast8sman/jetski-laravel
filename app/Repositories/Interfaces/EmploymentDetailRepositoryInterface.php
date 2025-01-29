<?php

namespace App\Repositories\Interfaces;

interface EmploymentDetailRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(array $data, int $user_id);
}