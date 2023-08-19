<?php

use Illuminate\Database\Seeder;
use App\Events\SeedingStarted;
use App\Events\SeedingEnded;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // fire seeding started event
        event(new SeedingStarted());

        // seed tables
        $appEnvironment = env('APP_ENV');
        if ($appEnvironment == 'local') {
            $this->call([
                LanguagesTableSeeder::class,
                StatesTableSeeder::class,
                UserTypesTableSeeder::class,
                UsersTableSeeder::class,
                StationsTableSeeder::class,
            ]);
        } else if ($appEnvironment == 'staging') {
        } else {

        }

         $this->call([
            LanguagesTableSeeder::class,
            StatesTableSeeder::class,
            UserTypesTableSeeder::class,
            UsersTableSeeder::class,
            StationsTableSeeder::class,
        ]);

        // fire seeding ended event
        event(new SeedingEnded());
    }
}