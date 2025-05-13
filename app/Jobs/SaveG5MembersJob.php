<?php

namespace App\Jobs;

use App\Models\User;
use App\Repositories\MemberRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SaveG5MembersJob implements ShouldQueue
{
    use Queueable;

    private $data;
    private $user;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->user = new MemberRepository(new User());
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $customer = $this->data;
        if(empty($customer['Phone'])){
            $customer['Phone'] = null;
        }
        if(empty($customer['Email'])){
            return;
        }
        if(empty($customer['Mobile'])){
            $customer['Mobile'] = null;
        }

        $email_array = explode(',', $customer['Email']);
        $email = trim(strval(array_shift($email_array)));
        $other_emails = !empty($email_array) ? trim(strval(join(',', $email_array))) : '';

        $sortData = [
            ['g5_id' => $customer['CustomerID']],
            ['email' => $email],
            ['phone' => $customer['Mobile']]
        ];

        $user = $this->user->findByOrFirst($sortData);
        if($user){
            if(empty($user->g5_id)){
                $user->update(['g5_id' => $customer['CustomerID']]);
            }
            $user->wallet()->update(['balance' => $customer['Debt'] <= 0 ? abs($customer['Debt']) : -1 * abs($customer['Debt'])]);
            return;
        }

        // $balance = $customer['Debt'] < 0 ? abs($customer['Debt']) : -1 * abs($customer['Debt']);
        // $this->user->store([
        //     'g5_id' => $customer['CustomerID'],
        //     'firstname' => $customer['CustomerName'],
        //     'lastname' => $customer['FamilyName'],
        //     'phone' => $customer['Mobile'] != '' ? $customer['Mobile'] : $customer['Phone'],
        //     'gender' => ucfirst($customer['Sex']),
        //     'marital_status' => ($customer['MartialStatus']) ? ucfirst($customer['MartialStatus']) : 'Single',
        //     'address' => $customer['Street'] . ' ' .  $customer['City'] . ' ' . $customer['State'],
        //     'photo' => "https://lagos-jetski-files.s3.us-east-2.amazonaws.com/ljs-placeholder.png",
        //     'dob' => Carbon::parse($customer['BirthDay'])->format('Y-m-d'),
        //     'email' => $email,
        //     'other_emails' => $other_emails,
        //     'membership_id' => "NULL"
        // ], $balance);
    }
}
