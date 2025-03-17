<?php

namespace App\Repositories;

use App\Models\MembershipType;
use App\Repositories\Interfaces\MembershipTypeRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MembershipTypeRepository extends AbstractRepository implements MembershipTypeRepositoryInterface
{
    public $errors;

    public function __construct(MembershipType $type)
    {
        parent::__construct($type);
    }

    public function store(array $request){
        $all = $request;

        $all['uuid'] = Str::uuid().'-'.time();

        if(!$type = $this->create($all)){
            $this->errors = "Membership Type creation failed!";
            return false;
        }

        return $type;
    }
}