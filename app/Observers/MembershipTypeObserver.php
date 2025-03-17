<?php

namespace App\Observers;

use App\Models\MembershipType;
use App\Models\User;
use App\Models\UserMembership;

class MembershipTypeObserver
{
    /**
     * Handle the MembershipType "created" event.
     */
    public function created(MembershipType $membershipType): void
    {
        //
    }

    /**
     * Handle the MembershipType "updated" event.
     */
    public function updated(MembershipType $membershipType): void
    {
        //
    }

    /**
     * Handle the MembershipType "deleted" event.
     */
    public function deleted(MembershipType $type): void
    {
        $memberships = UserMembership::where('membership_id', $type->id);
        if($memberships->count() > 0){
            foreach($memberships->get() as $membership){
                $membership->membership_id = null;
                $membership->save();
            }
        }
        $users = User::where('membership_id', $type->id);
        if($users->count() > 0){
            foreach($users as $user){
                $user->membership_id = null;
                $user->save();
            }
        }
    }

    /**
     * Handle the MembershipType "restored" event.
     */
    public function restored(MembershipType $membershipType): void
    {
        //
    }

    /**
     * Handle the MembershipType "force deleted" event.
     */
    public function forceDeleted(MembershipType $membershipType): void
    {
        //
    }
}