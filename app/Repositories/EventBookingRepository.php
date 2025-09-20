<?php

namespace App\Repositories;

use App\Models\EventTicketPricing;
use App\Models\JetskiEvent;
use App\Models\JetskiEventBooking;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Repositories\Interfaces\EventBookingRepositoryInterface;
use App\Services\G5PosService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stringable;

class EventBookingRepository extends AbstractRepository implements EventBookingRepositoryInterface
{
    public $errors;

    public function __construct(JetskiEventBooking $booking)
    {
        parent::__construct($booking);
    }

    public function store(array $data, $event_id, $user_id)
    {
        $user = User::find($user_id);
        if(empty($user)){
            $this->errors = "User not found";
            return false;
        }
        if(!empty($user->parent_id)){
            $this->errors = "User is a child account";
            return false;
        }

        $event = JetskiEvent::find($event_id);
        if(empty($event)){
            $this->errors = "Event not found";
            return false;
        }

        $pricing_id = [];
        $pricing = json_decode($event->tickets_pricing, true);
        foreach($pricing as $price){
            $pricing_id[] = $price['id'];
        }

        $tickets = [];
        foreach($data as $ticket){
            $e_ticket = EventTicketPricing::where('uuid', $ticket['ticket_id'])->first();
            if(empty($e_ticket)){
                continue;
            }
            if(in_array($e_ticket->id, $pricing_id)){
                $tickets[] = [
                    'ticket' => $e_ticket,
                    'quantity' => $ticket['quantity']
                ];
            }
        }

        if(empty($tickets)){
            $this->errors = "No valid ticket selected";
            return false;
        }
        if(!$order = $this->g5_order($tickets, $user)){
            return false;
        }

        $total_amount = 0;
        $total_quantity = 0;
        $bought_tickets = [];

        foreach($tickets as $ticket){
            $bought_tickets[] = [
                'ticket_id' => $ticket['ticket']->id,
                'unit_price' => $ticket['ticket']->price,
                'quantity' => $ticket['quantity'],
                'total_price' => $ticket['ticket']->price * $ticket['quantity']
            ];
            $total_amount += $ticket['ticket']->price * $ticket['quantity'];
            $total_quantity += $ticket['quantity'];
        }

        $booking = JetskiEventBooking::create([
            'uuid' => Str::uuid().' '.time(),
            'user_id' => $user->id,
            'booking_reference' => 'EBK-'.time().'-'.rand(1000, 9999),
            'g5_id' => $order['g5_id'],
            'g5_order_number' => $order['g5_order_number'],
            'jetski_event_id' => $event->id,
            'tickets' => json_encode($bought_tickets),
            'total_quantity' => $total_quantity,
            'total_amount' => $total_amount,
            'status' => 'Completed'
        ]);

        $wallet = $user->wallet;
        $wallet->balance -= $booking->total_amount;
        $wallet->save();

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'amount' => $booking->total_amount,
            'type' => 'Debit',
            'uuid' => Str::uuid().'-'.time(),
            'is_user_credited' => false,
            'reason' => 'Event Ticket Purchase',
            'reason_id' => $booking->id,
            'payment_processor' => 'G5 POS',
            'external_reference' => $booking->g5_id
        ]);

        return $booking;
    }

    private function g5_order(array $tickets, User $user){
        if(env('APP_ENV') == 'production'){
            $g5 = new G5PosService();
            $employee_code = config('g5pos.api_credentials.order_employee_code');
            $orderNumber = $g5->orderNumber("Take Out");
    
            $orderData = [
                'OrderNumber' => intval($orderNumber),
                'OrderMenuID' => 3,
                'UserID' => intval($employee_code),
                'CustomerID' => intval($user->g5_id)
            ];
    
            $orderId = $g5->newOrder($orderData);
    
            $sel_items = [];
            foreach($tickets as $ticket){
                $sel_items[] = [
                    'ItemID' => intval($ticket['ticket']->g5_id),
                    'Quantity' => intval($ticket['quantity']),
                    'UsedPrice' => floatval($ticket['ticket']->price),
                    'CustomerNumber' => intval($user->g5_id),
                    'AffectedItem' => 0,
                    'VoidReasonID' => 0,
                    'Status' => 'selected',
                    'OrderbyEmployeeId' => intval($employee_code),
                    'PriceModeID' => 1,
                    'OrderingTime' => Carbon::now('Africa/Lagos')->format('Y-m-d'),
                    'ItemDescription' => $ticket['ticket']->name,
                    'ItemRemark' => '',
                    'inctax' => 0,
                    'SetMenu' => false
                ];
            }
    
             $saveData = [
                'OrderID' => intval($orderId),
                'selectedItems' => $sel_items
            ];
            $res = $g5->saveOrder($saveData);
    
            if(!filter_var($res, FILTER_VALIDATE_BOOLEAN)){
                $this->errors = "Order can't be processed";
                return false;
            }
    
            return ['g5_id' => $orderId, 'g5_order_number' => $orderNumber];
        } else {
            return ['g5_id' => 'TEST-'.rand(100000, 999999), 'g5_order_number' => 'TEST-'.rand(100000, 999999)];
        }
    }

    public function index($limit = 10, int $user_id)
    {
        $orderBy = [
            ['created_at', 'desc']
        ];
        $criteria = [
            ['user_id', '=', $user_id]
        ];
        $bookings = $this->findBy($criteria, $orderBy, $limit);
        return $bookings;
    }
}