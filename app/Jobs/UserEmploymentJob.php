<?php

namespace App\Jobs;

use App\Models\EmploymentDetail;
use App\Models\User;
use App\Models\UserDeliveryAddress;
use App\Repositories\EmploymentDetailRepository;
use App\Repositories\UserDeliveryAddressRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class UserEmploymentJob implements ShouldQueue
{
    use Queueable;

    private $row;
    private $user;
    private $repo;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, $row)
    {
        $this->row = $row;
        $this->user = $user;
        $this->repo = new EmploymentDetailRepository(new EmploymentDetail());
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [
            'employer' => $this->row['employer'],
            'position' => $this->row['position'],
            'industry' => $this->row['industry'],
            'address' => $this->row['office_address'],
            'email' => $this->row['email_w'],
            'phone' => $this->row['work_mobile_number'],
            'pa_name' => $this->row['personal_assistant_name'],
            'pa_email' => $this->row['pa_email'],
            'pa_phone' => $this->row['pa_mobile'],
        ];

        $this->repo->store($data, $this->user->id);
    }
}
