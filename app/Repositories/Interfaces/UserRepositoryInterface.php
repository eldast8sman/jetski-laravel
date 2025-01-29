<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface UserRepositoryInterface extends AbstractRepositoryInterface
{
    public function findByVerificationToken(string $token);

    public function activate(Request $request);

    public function findByEmail(string $email);

    public function update_photo(Request $request);

    public function resend_activation_link(Request $request);
}