<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'superadmin@parkvisi.id'],
            [
                'name' => 'Super Admin',
                'password' => 'GantiPasswordIni123',
            ]
        );

        // role & is_active di-set langsung supaya pasti tersimpan
        $user->role = User::ROLE_SUPERADMIN;
        $user->is_active = true;
        $user->email_verified_at = now();
        $user->save();
    }
}