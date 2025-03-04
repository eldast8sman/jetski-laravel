<?php

namespace App\Repositories\Interfaces;

interface EventBookingRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(array $data, $event_id, $user_id);

    public function index($limit=10, int $user_id);
}