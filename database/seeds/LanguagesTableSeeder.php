<?php

use Illuminate\Database\Seeder;
use App\Helpers\Seed;
use App\Models\Language;

class LanguagesTableSeeder extends Seeder
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
                'title' => 'Myanmar (Unicode)',
                'code' => 'mm-uni',
            ],
            [
                'title' => 'Myanmar (Zawgyi)',
                'code' => 'mm-zg',
            ],
            [
                'title' => 'English',
                'code' => 'en',
            ],
        ];

        Seed::insertData(Language::class, $rows);
    }
}