<?php

namespace App\Repositories;

use App\Events\UserRegistered;
use App\Mail\AddUserNotificationMail;
use App\Models\Product;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Services\FileManagerService;
use App\Services\G5PosService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MemberRepository extends AbstractRepository implements MemberRepositoryInterface
{
    public $errors = "";

    public $user;

    public function __construct(User $user){
        parent::__construct($user);
        $this->user = $user;
    }

    public function fetch_g5_customers()
    {
        try {
            $service = new G5PosService();

            $customers = json_decode($service->getCustomers([]), true);

            foreach($customers as $customer){
                if(empty($customer['Phone'])){
                    $customer['Phone'] = null;
                }
                if(empty($customer['Email'])){
                    continue;
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

                $user = $this->findByOrFirst($sortData);
                if($user){
                    if(empty($user->g5_id)){
                        $user->update(['g5_id' => $customer['CustomerID']]);
                    }
                    $user->wallet()->update(['balance' => $customer['Debt'] < 0 ? abs($customer['Debt']) : -1 * abs($customer['Debt'])]);
                    continue;
                }

                $balance = $customer['Debt'] < 0 ? abs($customer['Debt']) : -1 * abs($customer['Debt']);
                $user = $this->store([
                    'g5_id' => $customer['CustomerID'],
                    'firstname' => $customer['CustomerName'],
                    'lastname' => $customer['FamilyName'],
                    'phone' => $customer['Mobile'] != '' ? $customer['Mobile'] : $customer['Phone'],
                    'gender' => ucfirst($customer['Sex']),
                    'marital_status' => ($customer['MartialStatus']) ? ucfirst($customer['MartialStatus']) : 'Single',
                    'address' => $customer['Street'] . ' ' .  $customer['City'] . ' ' . $customer['State'],
                    'photo' => "https://avatars.dicebear.com/api/initials/" . $customer['CustomerName'] . ".svg",
                    'dob' => Carbon::parse($customer['BirthDay'])->format('Y-m-d'),
                    'email' => $email,
                    'other_emails' => $other_emails,
                    'membership_id' => !empty($product = Product::where('name', 'JetSki')->first()) ? $product->id : null
                ], $balance);
                
            }

            return true;
        } catch(Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }

    public function keep(array $data){
        $user = $this->findFirstBy(['email' => $data['email']]);
        if(empty($user)){
            $user = $this->store($data, 0);
        } else {
            $user = $this->update_user($user, $data);
        }

        return $user;
    }

    public function store(array $data, $balance=null)
    {
        $data['uuid'] = Str::uuid().'-'.time();
        $data['verification_token'] = mt_rand(111111, 999999);
        $data['verification_token_expiry'] = date('Y-m-d H:i:s', time() + (60 * 60 * 24));

        $user = $this->create($data);
        if(!$user){
            return false;
        }

        if(($balance !== null) and empty($user->parent_id)){
            Wallet::create([
                'uuid' => Str::uuid().'-'.time(),
                'user_id' => $user->id,
                'balance' => $balance
            ]);
        }
        
        UserRegistered::dispatch($user);        

        return $user;
    }

    public function index($limit, $search="")
    {
        $users = $this->user->whereParent();
        if(!empty($search)){
            $names = explode(' ', $search);
            foreach($names as $name){
                $name = trim($name);

                $users = $users->where(function($query) use ($name){
                    $query->where('firstname', 'like', '%'.$name.'%')
                        ->orWhere('lastname', 'like', '%'.$name.'%');
                });
            }
        }
        
        $users = $users->orderBy('firstname', 'asc')
                ->orderBy('lastname', 'asc')->orderBy('created_at', 'asc')
                ->paginate($limit);

        return $users;
    }    

    public function all_members($limit=null)
    {
        $order = [
            ['firstname', 'asc'],
            ['lastname', 'asc'],
            ['created_at', 'asc']
        ];

        $users = $this->all($order, $limit);
        return $users;
    }

    public function resend_activation_link($uuid)
    {
        $user = $this->findByUuid($uuid);
        if(empty($user)){
            $this->errors = "No User was fetched";
            return false;
        }
        if($user->email_verified == 1){
            $this->errors = "Email already verified";
            return false;
        }

        $user->verification_token = mt_rand(111111, 666666);
        $user->verification_token_expiry = date('Y-m-d H:i:s', time() + (60 * 60 * 24));
        $user->save();
        $user->name = $user->firstname;
        Mail::to($user)->send(new AddUserNotificationMail($user->name, $user->verification_token, $user->email));

        return true;
    }

    public function update_user(User $user, array $data)
    {
        $user->update($data);
        return $user;
    }

    public function update_member(Request $request, User $user){
        if(isset($request->email) and !empty($request->email)){
            $em_found = $this->findFirstBy([
                ['email', '=', $request->email],
                ['id', '!=', $user->id]
            ]);
            if(!empty($em_found)){
                $this->errors = $em_found;
                return false;
            }
        }

        if(isset($request->phone) and !empty($request->phone)){
            $em_found = $this->findFirstBy([
                ['phone', '=', $request->phone],
                ['id', '!=', $user->id]
            ]);
            if(!empty($em_found)){
                $this->errors = "Duplicate Phone Number";
                return false;
            }
        }
        $data = $request->except(['photo']);
        if(isset($request->photo) and !empty($request->photo)){
            $photo = FileManagerService::upload_file($request->file('photo', env('FILESYSTEM_DISK')));
            if(!$photo){
                $this->errors = "Photo upload failed";
                return false;
            }
            $data['photo'] = $photo->url;
            if(!empty($user->photo)){
                $old_photo = $user->photo;
            }
        }

        $user->update($data);
        if(isset($old_photo)){
            if($old_photo = FileManagerService::findByUrl($old_photo)){
                FileManagerService::delete($old_photo->id);
            }
        }

        return $user;
    }

    public function store_user(Request $request)
    {
        $data = $request->except(['photo']);
        if(isset($request->photo) and !empty($request->photo)){
            $photo = FileManagerService::upload_file($request->file('photo', env('FILESYSTEM_DISK')));
            if(!$photo){
                $this->errors = "Photo upload failed";
                return false;
            }
            $data['photo'] = $photo->url;
        }

        $user = $this->store($data, 0);
        return $user;
    }

    public function user_activation(Request $request, $uuid){
        $user = $this->findByUuid($uuid);
        if(empty($user)){
            $this->errors = "No User was fetched";
            return false;
        }

        $user->can_use = $request->status;
        $user->save();
        if($user->can_use == 0){
            if(empty($user->parent_id)){
                $relations = $this->findBy(['parent_id' => $user->id]);
                if(!empty($relations)){
                    foreach($relations as $relation){
                        $relation->can_use = 0;
                        $relation->save();
                    }
                }
            }
        }
    }

    public function fetch_member_by_param($key, $value)
    {
        $user = $this->findFirstBy([$key => $value]);
        return $user;
    }
}