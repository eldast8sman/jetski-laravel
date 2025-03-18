<?php

namespace App\Jobs;

use App\Models\MembershipType;
use App\Models\Product;
use App\Models\User;
use App\Repositories\MemberRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SaveMembershipJob implements ShouldQueue
{
    use Queueable;

    private $row;
    private $repo;

    /**
     * Create a new job instance.
     */
    public function __construct($row)
    {
        $this->row = $row;
        $this->repo = new MemberRepository(new User());
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $row = $this->row;
        $mem_type = $row['membership_type'];
        $mems = [];
        if(!empty($mem_type)){
            $types = explode('/', $mem_type);
            foreach($types as $type){
                $type = trim($type);
                $membership = MembershipType::where('name', $type)->first();
                if(!empty($membership)){
                    $mems[] = $membership->uuid;
                }
            }
        }
        $data = [
            'membership_id' => $row['id_number'],
            'firstname' => $row['first_name'],
            'lastname' => $row['last_name'],
            'phone' => $row['mobile_number'],
            'private_phone' => $row['private_mobile_number'],
            'gender' => ucfirst($row['gender']),
            'address' => $row['address'],
            'nationality' => $row['nationality'],
            'religion' => $row['religion'],
            'photo' => "https://lagos-jetski-files.s3.us-east-2.amazonaws.com/ljs-placeholder.png",
            'dob' => Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($row['birthday'] - 2)->toDateString(),
            'email' => $row['email_address'],
            'membership_type_id' => !empty($mems) ? join(',', $mems) : null
        ];
        if(isset($row['parent_id']) and !empty($row['parent_id'])){
            $parent = User::where('membership_id', $row['parent_id'])->first();
            if(!empty($parent)){
                $data['parent_id'] = $parent->id;
            }
        }
        // if(isset($membership_id)){
        //     $data['membership_id'] = $membership_id;
        // }

        $user = $this->repo->keep($data);
        if($user){
            dispatch(new UserEmploymentJob($user, $row));
            dispatch(new MembershipInfoJob($user, $row));
            dispatch(new UserWaterCraftJob($user, $row));
        }
    }
}
