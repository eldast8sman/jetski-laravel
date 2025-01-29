<?php

namespace App\Http\Controllers;

use App\Jobs\SparkleWebhookJob;
use App\Services\G5PosService;
use Illuminate\Http\Request;

class SparkleController extends Controller
{
    public function __construct(private G5PosService $g5)
    {
        
    }

    public function webhook(Request $request){
        SparkleWebhookJob::dispatch($request->all());
        return $this->success_response("Webhook received");
    }
}
