<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\UserDeliveryAddressRepositoryInterface;
use App\Services\AuthService;
use Illuminate\Http\Request;

class UserDeliveryAddressController extends Controller
{
    private $repo;

    public function __construct(UserDeliveryAddressRepositoryInterface $repository)
    {
        $this->repo = $repository;
    }

    public function update(Request $request, $uuid)
    {
        $address = $this->repo->findFirstBy([
            'uuid' => $uuid,
            'user_id' => auth('user-api')->user()->id
        ]);

        if(empty($address)){
            return $this->failed_response("No Delivery Address was found", 404);
        }

        $address->update([
            'address' => $request->address
        ]);

        $service = new AuthService('user-api');
        return $this->success_response("Delivery Address updated successfully", $service->logged_in_user());
    }

    public function destroy($uuid)
    {
        $address = $this->repo->findFirstBy([
            'uuid' => $uuid,
            'user_id' => auth('user-api')->user()->id
        ]);

        if(empty($address)){
            return $this->failed_response("No Delivery Address was found", 404);
        }

        $address->delete();

        $service = new AuthService('user-api');
        return $this->success_response("Delivery Address deleted successfully", $service->logged_in_user());
    }
}
