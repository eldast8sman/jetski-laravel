<?php

namespace App\Repositories;

use App\Models\MembershipInformation;
use App\Repositories\Interfaces\UserWatercraftRepositoryInterface;
use Illuminate\Support\Str;

class UserWatercraftRepository extends AbstractRepository implements UserWatercraftRepositoryInterface
{
    public function __construct(MembershipInformation $info){
        parent::__construct($info);
    }

    public function store(array $data, int $user_id)
    {
        $info = $this->findFirstBy(['user_id' => $user_id]);
        if(!empty($info)){
            $info->update($data);
        } else {
            $data['user_id'] = $user_id;
            $data['uuid'] = Str::uuid().'-'.time();

            $info = $this->create($data);
        }

        return $info;
    }
}