<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface AnnouncementRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(Request $request);

    public function index($limit=10);

    public function show(string $uuid);
}