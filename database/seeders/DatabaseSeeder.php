<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $arrUsers = [
            [
                'name' => 'Administrator',
                'email' => 'admin@test.com',
                'password' => bcrypt('root123'),
                'role' => 'administrator',
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@test.com',
                'password' => bcrypt('root123'),
                'role' => 'manager',
            ],
            [
                'name' => 'User 1',
                'email' => 'user1@test.com',
                'password' => bcrypt('root123'),
                'role' => 'user',
            ],

        ];

        foreach ($arrUsers as $user) {
            $newUser = new User();
            $newUser->name = $user['name'];
            $newUser->email = $user['email'];
            $newUser->password = $user['password'];
            $newUser->role = $user['role'];
            $newUser->save();

            $newUser->orders()->createMany([
                ['user_id' => $newUser->id, 'created_at' => now()],
                ['user_id' => $newUser->id, 'created_at' => now()],
            ]);
        }
    }
}
