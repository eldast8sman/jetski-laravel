<?php

namespace App\Imports;

use App\Jobs\SaveMembershipJob;
use App\Models\User;
use App\Repositories\MemberRepository;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MembershipImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach($collection as $row){
            if(!empty($row['first_name']) and !empty($row['last_name'])){
                dispatch(new SaveMembershipJob($row));
            }
        }
        $repo = new MemberRepository(new User());
        $repo->fetch_g5_customers();
    }
}
