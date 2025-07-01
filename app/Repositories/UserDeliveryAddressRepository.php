<?php

namespace App\Repositories;

use App\Models\UserDeliveryAddress;
use App\Repositories\Interfaces\UserDeliveryAddressRepositoryInterface;

class UserDeliveryAddressRepository extends AbstractRepository implements UserDeliveryAddressRepositoryInterface
{
    public $errors;

    public function __construct(UserDeliveryAddress $address)
    {
        parent::__construct($address);
    }

    
}