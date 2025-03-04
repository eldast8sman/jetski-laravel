<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface JetskiEventRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(Request $request);

    public function index($search="", $from="", $to="", $sort="desc", $limit=10, $user=false);

    public function upcoming_events($search="", $from="", $to="", $sort="asc", $limit=10, $user=false);

    public function show($uuid);

    public function update_event(Request $request, string $uuid);

    public function delete_event(string $uuid);

    public function event_tickets($search="");
}