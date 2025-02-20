<?php

namespace App\Jobs;

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
        if(!empty($mem_type)){
            $membership = Product::where('category', 'Infrastructure')->where('name', $mem_type)->first();
            if(!empty($membership)){
                $membership_id = $membership->id;
            }
        }
        $data = [
            'g5_id' => $row['id_number'],
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
        ];
        if(isset($membership_id)){
            $data['membership_id'] = $membership_id;
        }

        $user = $this->repo->keep($data);
        if($user){
            dispatch(new UserEmploymentJob($user, $row));
            dispatch(new MembershipInfoJob($user, $row));
            dispatch(new UserWaterCraftJob($user, $row));
        }
    }
}
