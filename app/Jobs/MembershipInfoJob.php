<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserMembership;
use App\Repositories\MembershipInformationRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class MembershipInfoJob implements ShouldQueue
{
    use Queueable;

    private $user;
    private $row;
    private $repo;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, $row)
    {
        $this->user = $user;
        $this->row = $row;
        $this->repo = new MembershipInformationRepository(new UserMembership());
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [
            'membership_id' => $this->user->membership_id,
            'amount' => !empty($this->row['membership_rate']) ? str_replace(['$', ','], ['', ''], $this->row['membership_rate']) : 0,
            'payment_date' => Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($this->row['payment_date'] - 2)->toDateString(),
            'date_joined' => Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($this->row['member_joining_date'] - 2)->toDateString(),
            'expiry_date' => Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($this->row['expiry'] - 2)->toDateString(),
            'membership_notes' => $this->row['member_notes'],
            'active_diver' => $this->row['active_diver'],
            'padi_level' => $this->row['padi_certification_level'],
            'padi_number' => $this->row['padi_certification_number'],
            'referee1' => $this->row['reference_1'],
            'referee2' => $this->row['reference_2'],
            'referee3' => $this->row['reference_3'],
            'referee4' => $this->row['reference_4'],
        ];

        $this->repo->store($data, $this->user->id);
    }
} 