<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface EventRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(Request $request);

    public function events($limit=10);

    public function userEvents(int $user_id, int $limit);

    public function userPastEvents(int $user_id, int $limit);

    public function pastEents($limit=10);

    public function updateEvent(Request $request, string $id);

    public function deleteEvent(string $id);

    public function showEvent(string $id);
}