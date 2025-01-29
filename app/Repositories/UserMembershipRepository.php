<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\User;
use App\Repositories\Interfaces\UserMembershipRepositoryInterface;
use Illuminate\Http\Request;

class UserMembershipRepository extends AbstractRepository implements UserMembershipRepositoryInterface
{
    public $errors = "";

    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function membership_types()
    {
        $model = new AbstractRepository(new Product());
        $services = $model->findBy(['category' => 'Infrastructure']);
        return $services;
    }

    public function fetch_membership()
    {
        $user = $this->find(auth('user-api')->user()->id);
        return $user;
    }

    public function update_membership(Request $request)
    {
        $user = $this->find(auth('user-api')->user()->id);
        $type = Product::where(['category' => 'Infrastructure', 'id' => $request->membership_id])->first();
        if(empty($type)){
            $this->errors = "Wrong Membership Type";
            return false;
        }
        $user->membership_id = $request->membership_id;
        $user->save();
        $info = $user->membership_information()->first();
        $info->update($request->except(['membership_id']));

        return $user;
    }
}