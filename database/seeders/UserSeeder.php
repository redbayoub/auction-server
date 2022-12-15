<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        User::firstOrCreate(
            ['id' => 1],
            User::factory(1)->raw([
                'username' => 'user1',
                'password' => Hash::make('user2'),
                'isAdmin' => false,
            ])[0]
        );

        User::firstOrCreate(
            ['id' => 2],
            User::factory(1)->raw([
                'username' => 'admin1',
                'password' => Hash::make('admin2'),
                'isAdmin' => true,
            ])[0]
        );
    }
}
