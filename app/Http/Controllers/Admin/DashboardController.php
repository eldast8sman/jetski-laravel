<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BookingResource;
use App\Http\Resources\Admin\NotificationResource;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Repositories\Interfaces\MenuRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $booking;
    private $order;
    private $notification;
    private $product;

    public function __construct(BookingRepositoryInterface $booking, OrderRepositoryInterface $order, NotificationRepositoryInterface $notification, MenuRepositoryInterface $product)
    {
        $this->booking = $booking;
        $this->order = $order;
        $this->notification = $notification;
        $this->product = $product;
    }

    public function index(){
        $notificateds = $this->notification->index(2);
        $notifications = !empty($this->notificateds) ? NotificationResource::collection($notificateds) : [];
        $bookings = BookingResource::collection($this->booking->adminIndex(4));
        $orders = $this->order->summary();
        $membership = $this->product->membership_summary();

        return $this->success_response("Dashboard details fetched successfully", [
            'notifications' => $notifications,
            'bookings' => $bookings,
            'booking_summary' => $this->booking->booking_summary(),
            'order_summary' => $orders,
            'membership_summary' => $membership
        ]);
    }
}
