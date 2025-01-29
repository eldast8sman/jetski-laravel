<?php

namespace App\Repositories;

use App\Models\EmploymentDetail;
use App\Repositories\Interfaces\EmploymentDetailRepositoryInterface;
use Illuminate\Support\Str;

class EmploymentDetailRepository extends AbstractRepository implements EmploymentDetailRepositoryInterface
{
    public function __construct(EmploymentDetail $detail){
        parent::__construct($detail);
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