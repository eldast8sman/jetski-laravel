<?php

namespace App\Repositories\Interfaces;

use App\Models\Admin;
use Illuminate\Http\Request;

interface AdminRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(Request $request);

    public function activate(Request $request);

    public function findByEmail(string $email);

    public function findByVerificationToken(string $token);

    public function forgot_password(Request $request);

    public function reset_password(Request $request);

    public function update_account_details(Admin $admin, Request $request);

    public function update_profile(Admin $admin, Request $request);

    public function admin(Admin $admin);

    public function all_admins();

    public function fetch_by_uuid(string $uuid);

    public function update_admin(string $uuid, Request $request);

    public function delete_admin(string $uuid);
}