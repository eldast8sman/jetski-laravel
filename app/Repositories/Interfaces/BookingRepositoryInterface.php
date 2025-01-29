<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface BookingRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(Request $request);

    public function index($limit=10);

    public function adminIndex($limit=4);

    public function pastBookings($limit=10);

    public function allPastBookings($limit=10);

    public function showBooking(string $id);

    public function updateBooking(Request $request, string $id);

    public function deleteBooking(string $id);

    public function booking_summary();
}