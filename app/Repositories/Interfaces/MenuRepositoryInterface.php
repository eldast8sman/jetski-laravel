<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface MenuRepositoryInterface extends AbstractRepositoryInterface
{
    public function fetch_g5_menu();

    public function index(int $limit=9);

    public function fetch_menu(int $page);

    public function fetchByName(Request $request, int $limit=9);

    public function getModifiers($id);

    public function membership_summary();
}