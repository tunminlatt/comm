<?php

use Illuminate\Database\Seeder;
use App\Helpers\Seed;
use App\Models\UserType;

class UserTypesTableSeeder extends Seeder
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
                'title' => 'Super Admin',
            ],
            [
                'title' => 'Station Manager',
            ]
        ];

        Seed::insertData(UserType::class, $rows);
    }
}
