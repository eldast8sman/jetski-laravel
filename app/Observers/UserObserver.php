<?php

namespace App\Observers;

use App\Models\MembershipType;
use App\Models\User;
use App\Models\UserMembership;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $mems = $user->membership_type_id;
        if(!empty($mems)){
            $membership_ids = explode(',', $mems);
            foreach($membership_ids as $membership_id){
                $users = User::where('membership_id', 'like', '%'.$membership_id.'%')->count();
                $membership = MembershipType::where('uuid', $membership_id);
                if(!empty($membership)){
                    $membership->update(['total_members' => $users]);
                }
            }
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $mems = $user->membership_type_id;
        if(!empty($mems)){
            $membership_ids = explode(',', $mems);
            foreach($membership_ids as $membership_id){
                $users = User::where('membership_type_id', 'like', '%'.$membership_id.'%')->count();
                $membership = MembershipType::where('uuid', $membership_id);
                if(!empty($membership)){
                    $membership->update(['total_members' => $users]);
                }
            }
        }

        $mem_info = UserMembership::where('user_id', $user->id)->first();
        if(!empty($mem_info)){
            $mem_info->membership_type_id = $mems;
            $mem_info->save();
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $mems = $user->membership_type_id;
        if(!empty($mems)){
            $membership_ids = explode(',', $mems);
            foreach($membership_ids as $membership_id){
                $users = User::where('membership_type_id', 'like', '%'.$membership_id.'%')->count();
                $membership = MembershipType::where('uuid', $membership_id);
                if(!empty($membership)){
                    $membership->update(['total_members' => $users]);
                }
            }
        }
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
