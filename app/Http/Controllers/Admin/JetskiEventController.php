<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEventRequest;
use App\Repositories\Interfaces\JetskiEventRepositoryInterface;
use App\Services\G5PosService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JetskiEventController extends Controller
{
    protected $event;

    public function __construct(JetskiEventRepositoryInterface $event)
    {
        $this->event = $event;
    }

    public function refresh_ticketing(){
        try {
            $service = new G5PosService();

            $tickets = $service->getMenu([
                'ScreenID' => 53,
                'Type' => 3
            ]);

            dispatch(new StoreEventRequest($tickets));

            return $this->success_response('Ticketing refreshed successfully');
        } catch(Exception $e){
            Log::error('Refresh Ticketing: '.$e->getMessage());
            return $this->failed_response('Refresh failed. Check Logs for details');
        }
    }
}
