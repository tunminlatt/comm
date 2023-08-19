<?php

use Illuminate\Database\Seeder;
use App\Helpers\Seed;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@communityvoice.com',
                'email_verified_at' => Seed::generateCurrentDate(),
                'password' => Seed::getDefaultPassword(),
                'user_type_id' => 1,
                'remember_token' => Seed::generateRememberToken()
            ],
		];

        Seed::insertData(User::class, $rows);
    }
}