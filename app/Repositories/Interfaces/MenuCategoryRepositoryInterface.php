<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface MenuCategoryRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(Request $request);

    public function index();

    public function show($uuid);

    public function update_category(Request $request, string $uuid);

    public function delete_category($uuid);
}