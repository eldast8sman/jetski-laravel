<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\SparkleResponse;
use App\Models\User;
use App\Services\SparkleService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SparkleUserRegistration implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $user = User::find($event->user->id);
        if(empty($user->parent_id)){
            try {
                $service = new SparkleService();
                $reference = "SPK_Jetski_".$user->uuid;
                $payload = [
                    "name" => "{$user->firstname} {$user->lastname}",
                    "external_reference" =>  $reference,
                    "email" => $user->email,
                    "bank_verification_number" => "01234567891",
                    "is_permanent" => (env('APP_ENV') == 'production') ? 1 : 0,
                    "is_active" => 1
                  ];

                if(env('APP_ENV') != 'production'){
                    $payload['expires_at'] = '2027-12-31 23:59:58';
                }

                $account = $service->createAccount($payload);
                if($account){
                    $details = $account['data']['account'];
                    $account_number = $details['account_number'];
                    $sparkle_id = $details['id'];
                    $user->update([
                        'account_number' => $account_number,
                        'sparkle_id' => $sparkle_id,
                        'external_sparkle_reference' => $reference
                    ]);
                } else {
                    Log::error("Sparkle Error");
                }
            } catch (Exception $e){
                Log::error($e->getMessage());
            }
        }
    }
}
