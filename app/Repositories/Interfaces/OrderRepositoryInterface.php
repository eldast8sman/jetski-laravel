<?php

namespace App\Repositories\Interfaces;

interface OrderRepositoryInterface extends AbstractRepositoryInterface
{
    public function fetch_g5_order($user_id);

    public function index($limit=4);

    public function past_orders($limit=15);

    public function make_order(array $data);

    public function pending_orders($limit=4);

    public function all_past_orders($limit=15);

    public function admin_search($limit=15);

    public function summary();
}