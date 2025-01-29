<?php

namespace App\Repositories\Interfaces;

interface NotificationRepositoryInterface extends AbstractRepositoryInterface
{
    public function index($limit = 10);

    public function store(array $data);
}