<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class G5PosService
{
    private $base_url;
    private $branch_id;
    private $workstation_id;
    private $password;
    private $employee_code;
    private $token;

    public function __construct()
    {
        $this->base_url = config('g5pos.api_credentials.base_url');
        $this->branch_id = config('g5pos.api_credentials.branch_id');
        $this->workstation_id = config('g5pos.api_credentials.workstation_id');
        $this->password = config('g5pos.api_credentials.password');
        $this->employee_code = config('g5pos.api_credentials.employee_code');

        $this->token = Cache::remember('g5pos_token', 1200, function () {
            $result =  $this->login();
            return $result["token"];
        });
    }

    public function login(){
        $data = [
          "EmployeeID" => 0,
          "BranchID" => 1,
          "Password" => "8887910",
          "EmployeeCode" => "999",
          "POS_Config_ID" => 0,
          "InvConfigId" => 0,
          "WorkstationId" => 1
        ];

        $response = Http::post(config('g5pos.api_credentials.login_url'), $data);
        return $this->response_handler($response);
    }

    public function newOrder(array $data) //Done
    {
      $data['BranchID'] = intval($this->branch_id);
      $data['WorkStationID'] = intval($this->workstation_id);
      $data['NumberOfCustomers'] = 1;
      $data['DeliveryDriverEmpID'] = 0;
      $data['AgentID'] = 0;
      $data['DeliveryStatus'] = 0;
  
      $response = Http::withToken($this->token)->post("{$this->base_url}/PosOrder/NewDeliveryOrder", $data);
      
      return json_decode($this->response_handler($response), true);
    }
  
    public function tip(array $data) //Done
    {
      $data['BranchID'] = $this->branch_id;
      $data['LineNo'] = 0;
  
      $response = Http::withToken($this->token)->post("{$this->base_url}/PosOrder/Tips", $data);
  
      return $this->response_handler($response);
    }
  
    public function saveOrder(array $data) //Done
    {
      $data['BranchID'] = intval($this->branch_id);
      $data['Workstation'] = intval($this->workstation_id);
  
      $response = Http::withToken($this->token)->post("{$this->base_url}/PosOrder/SaveOrder", $data);
      return $this->response_handler($response);
    }
  
    public function getOrders(array $data) //Done
    {
      $data['BranchID'] = $this->branch_id;
  
      $response = Http::withToken($this->token)->get("{$this->base_url}/PosOrder/GetOrderListbyCustIdDate/{$this->branch_id}/{$data['CustomerID']}/2000-01-01/{$data['ToDate']}");
      return $this->response_handler($response);
    }
  
    public function getOrderDetails(array $data) //Done
    {
      $data['BranchID'] = $this->branch_id;
  
      $response = Http::withToken($this->token)->get("{$this->base_url}/PosOrder/GetOrderItemsList/{$this->branch_id}/{$data['OrderID']}");
      return $this->response_handler($response);
    }
  
    public function getModifiers($modifier_id) //Done
    {
      $response = Http::withToken($this->token)->get("{$this->base_url}/PosOrder/GetModifierList/{$modifier_id}");
  
      return $this->response_handler($response);
    }
  
    public function getMenu(array $data) //Done
    {
      $response = Http::withToken($this->token)->get("{$this->base_url}/PosOrder/GetScreenItems/{$this->branch_id}/{$data['ScreenID']}/{$data['Type']}");
      return $this->response_handler($response);
    }
  
    public function getCustomers(array $data) //Done
    {
      $data['BranchID'] = $this->branch_id;
  
      $response = Http::withToken($this->token)->get("{$this->base_url}/PosOrder/GetCustomers/{$this->branch_id}");
  
      return $this->response_handler($response);
    }
  
    public function getOrderNumber() //Done
    {
      $response = Http::withToken($this->token)->withHeaders(
        ['Content-Type' => 'application/json']
      )->get("{$this->base_url}/PosOrder/GetDeliveryOrderNo/{$this->branch_id}");
  
      return json_decode($this->response_handler($response), true);
    }

    public function orderNumber($type="Delivery")
    {
      if($type == "Delivery"){
        $order = 2;
      } elseif($type == "Take Out"){
        $order = 3;
      } else {
        return false;
      }
      $response = Http::withToken($this->token)->get("{$this->base_url}/PosOrder/GetLastOrderNumber/{$order}");
    }

    private function response_handler($response){
        if ($response->failed()) {
            Log::error('G5PosService error response:', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return $response->throw()->json();
        }
    
        $result = json_decode($response->body(), true);
    
        if ($result['statusCode'] > 299) {
            return $response->throw()->json();
        }
    
        if (array_key_exists('data', $result)) {
            return $result['data'];
        }
    
        return $result;
    }

    public function payByCustomer(WalletTransaction $trans, int $user_id)
    {
      $data = [
        'BranchID' => (int)$this->branch_id,
        'CustomerID' => (int)$user_id,
        'Amount' => (float)$trans->amount,
        'PayDate' => $trans->created_at,
        'PayTypeId' => 28
      ];

      $response = Http::withToken($this->token)->post("{$this->base_url}/PosOrder/PayByCustomer", $data);
      return $this->response_handler($response);
    }
}