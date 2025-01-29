<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInviteRequest;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Repositories\Interfaces\InviteRepositoryInterface;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    private $invite;

    public function __construct(InviteRepositoryInterface $invite)
    {
        $this->invite = $invite;
    }

    public function show($uuid){
        if(empty($booking = $this->invite->booking($uuid))){
            abort(404, "Wrong Link");
        }

        return view('invite', compact("booking")) ;
    }

    public function store(StoreInviteRequest $request, $id){
        if(!$this->invite->store($request, $id)){
            return redirect()->back()->with('error', $this->invite->errors);
        }

        return redirect()->back()->with('message', 'Invitation accepted');
    }
}
