<?php

namespace App\Repositories\Interfaces;

interface MembershipInformationRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(array $data, int $user_id);
}