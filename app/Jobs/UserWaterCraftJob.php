<?php

namespace App\Jobs;

use App\Models\MembershipInformation;
use App\Models\User;
use App\Repositories\UserWatercraftRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class UserWaterCraftJob implements ShouldQueue
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
        $this->repo = new UserWatercraftRepository(new MembershipInformation());
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [
            'title' => $this->row['type_of_watercraft'],
            'make' => $this->row['watercraft_make'],
            'model' => $this->row['watercraft_model'],
            'hin_number' => ($this->row['watercraft_hin'] == 'N/A') ? null : $this->row['watercraft_hin'],
            'year' => $this->row['watercraft_year'],
            'nwa' => ($this->row['niwa_registration_number'] == 'N/A') ? null : $this->row['niwa_registration_number'],
            'nwa_expiry' => $this->row['niwa_expriy']
        ];

        $this->repo->store($data, $this->user->id);
    }
}
