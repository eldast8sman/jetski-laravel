<?php

namespace App\Repositories;

use App\Events\UserRegistered;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\Interfaces\UserRelativeRepositoryInterface;
use App\Services\FileManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserRelativeRepository extends AbstractRepository implements UserRelativeRepositoryInterface
{
    public $errors = "";
    
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    private function check(){
        if(!empty(auth('user-api')->user()->parent_id)){
            $this->errors = "As a relative, you do not have access to this feature";
            return false;
        }

        return true;
    }

    public function store(Request $request)
    {
        if(!$this->check()){
            return false;
        }
        $all = $request->except(['photo']);
        if(!empty($request->photo)){
            $photo = FileManagerService::upload_file($request->file('photo'), env('FILESYSTEM_DISK'));
            if($photo){
                $all['photo'] = $photo->url;
            }
        }
        $all['parent_id'] = auth('user-api')->user()->id;

        $repo = new MemberRepository(new User());
        $user = $repo->store($all);

        return $user;
    }

    public function getRelatives()
    {
        if(!$this->check()){
            return false;
        }

        $data = ['parent_id' => auth('user-api')->user()->id];
        $relatives = $this->findBy($data);

        return $relatives;
    }

    public function getRelative($id)
    {
        if(!$this->check()){
            return false;
        }

        $data = ['parent_id' => auth('user-api')->user()->id, 'uuid' => $id];
        $relative = $this->findFirstBy($data);
        if(empty($relative)){
            $this->errors = "No Relative was fetched";
            return false;
        }

        return $relative;
    }

    public function updateRelative(Request $request, string $id)
    {
        if(!$this->check()){
            return false;
        }

        $data = ['parent_id' => auth('user-api')->user()->id, 'uuid' => $id];
        $old_rel = $this->findFirstBy($data);
        if(empty($old_rel)){
            $this->errors = "No Relative was fetched";
            return false;
        }
        $new_photo = false;
        $all = $request->except(['photo']);
        if(!empty($request->photo)){
            $photo = FileManagerService::upload_file($request->file('photo'), env('FILESYSTEM_DISK'));
            if($photo){
                $all['photo'] = $photo->url;
                $new_photo = true;
            }
        }
        $relative = $this->update($id, $all);
        if(!empty($old_rel->photo) and $new_photo){
            $old_photo = FileManagerService::findByUrl($old_rel->photo);
            if(!empty($old_photo)){
                FileManagerService::delete($old_photo->id);
            }
        }
        return $relative;
    }

    public function user_activation(Request $request, $uuid)
    {
        $user = $this->findFirstBy(['parent_id' => auth('user-api')->user()->id, 'uuid' => $uuid]);
        if(empty($user)){
            $this->errors = "No Relative was fetched";
            return false;
        }

        $user->can_use = $request->status;
        $user->save();
        return $user;
    }

    public function deleteRelative(string $id)
    {
        if(!$this->check()){
            return false;
        }
        $data = ['parent_id' => auth('user-api')->user()->id, 'uuid' => $id];
        $relative = $this->findFirstBy($data);
        if(empty($relative)){
            $this->errors = "No Relative was found";
            return false;
        }

        $this->delete($relative);
        if(!empty($relative->photo)){
            $pics = FileManagerService::findByUrl($relative->photo);
            FileManagerService::delete($pics->id);
        }
        return true;
    }
}