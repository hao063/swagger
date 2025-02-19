<?php

namespace App\Services;

use App\Models\User;

class TestService
{
    public function execute()
    {
        return User::all()->first();
    }
}