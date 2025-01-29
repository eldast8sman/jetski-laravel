<?php

namespace App\Observers;

use App\Models\MembershipInformation;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

class UserObsever
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        MembershipInformation::create([
            'user_id' => $user->id,
            'uuid' => Str::uuid().'-'.time()
        ]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
