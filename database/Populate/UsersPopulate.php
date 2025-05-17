<?php

namespace Database\Populate;

use App\Models\User;

class UsersPopulate
{
    public static function populate()
    {
        $data = [
            'name' => 'Fulano',
            'email' => 'fulano@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'is_admin' => 0
        ];

        $user = new User($data);
        $user->save();

        $adminData = [
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'admin123',
            'password_confirmation' => 'admin123',
            'is_admin' => 1
        ];

        $admin = new User($adminData);
        $admin->save();
    }
}
