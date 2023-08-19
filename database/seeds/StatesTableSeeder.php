<?php

use Illuminate\Database\Seeder;
use App\Helpers\Seed;
use App\Models\State;

class StatesTableSeeder extends Seeder
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
                'title' => 'Pending'
            ],
            [
                'title' => 'Accepted'
            ],
            [
                'title' => 'Rejected'
            ],
        ];

        Seed::insertData(State::class, $rows);
    }
}