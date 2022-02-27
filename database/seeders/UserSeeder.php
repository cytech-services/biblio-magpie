<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Christopher Graham',
            'email' => 'chris@cytech.services',
            'password' => Hash::make('uCfTHiHamRvy'),
        ]);
        $user->assignRole('Super Admin');

        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@cytech.services',
            'password' => Hash::make('uCfTHiHamRvy'),
        ]);
        $user->assignRole('Admin');

        $user = User::create([
            'name' => 'Librarian User',
            'email' => 'librarian@cytech.services',
            'password' => Hash::make('uCfTHiHamRvy'),
        ]);
        $user->assignRole('Librarian');

        $user = User::create([
            'name' => 'User',
            'email' => 'user@cytech.services',
            'password' => Hash::make('uCfTHiHamRvy'),
        ]);
        $user->assignRole('User');
    }
}
