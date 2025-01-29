<?php

namespace App\Repositories;

use App\Models\UserMembership;
use App\Repositories\Interfaces\MembershipInformationRepositoryInterface;
use Illuminate\Support\Str;

class MembershipInformationRepository extends AbstractRepository implements MembershipInformationRepositoryInterface
{
    public function __construct(UserMembership $info)
    {
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
            $data['status'] = 1;

            $info = $this->create($data);
        }

        return $info;
    }
}