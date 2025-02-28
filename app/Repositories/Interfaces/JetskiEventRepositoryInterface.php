<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface JetskiEventRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(Request $request);

    public function index($search="", $from="", $to="", $sort="desc", $limit=10);

    public function upcoming_events($search="", $from="", $to="", $sort="asc", $limit=10);

    public function show($uuid);

    public function update_event(Request $request, $uuid);
}