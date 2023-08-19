<?php

use Illuminate\Database\Seeder;
use App\Helpers\Seed;
use App\Models\Station;
use App\Models\Volunteer;
use App\Models\Audio;
use App\Models\Programme;
use App\Models\User;

class StationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Station::truncate();
        Volunteer::truncate();
        Audio::truncate();
        Programme::truncate();

        Storage::deleteDirectory('stations');
        Storage::deleteDirectory('volunteers');
        Storage::deleteDirectory('audios');
        Storage::deleteDirectory('programmes');
        Storage::deleteDirectory('stationManagers');

        $seedLimit = Seed::getSeedLimit();

        // save station
        factory(Station::class, $seedLimit)->create()->each(function ($station) {
            $stationID = $station->id;
            Seed::insertImage('stations', 'stations/'. $stationID);

            // save station manager
            $station->stationManagers()->saveMany(factory(User::class, rand(1, 3))->make())->each(function ($user) use ($stationID) {
                Seed::insertImage('stationManagers', 'stationManagers/'. $user->id);

                // save programme
                $user->uploadedProgrammes()->saveMany(factory(Programme::class, rand(1, 3))->make(['station_id' => $stationID]))->each(function ($programme) {
                    Seed::insertImage('programmes/banner', 'programmes/'. $programme->id .'/banner');
                    Seed::insertImage('programmes/recording', 'programmes/'. $programme->id .'/recording');
                });
            });

            // save volunteer
            $station->volunteers()->saveMany(factory(Volunteer::class, rand(1, 3))->make())->each(function ($volunteer) use ($stationID) {
                Seed::insertImage('volunteers', 'volunteers/'. $volunteer->id);

                // save audio
                $volunteer->audios()->saveMany(factory(Audio::class, rand(1, 3))->make(['station_id' => $stationID]))->each(function ($audio) {
                    Seed::insertImage('audios/banner', 'audios/'. $audio->id .'/banner');
                    Seed::insertImage('audios/recording', 'audios/'. $audio->id .'/recording');

                    $users = User::where('user_type_id', 2)->pluck('id');
                    $userCount = count($users);
                    $audio->stationManagers()->attach($users->random(rand(1, $userCount)), [
                        'created_at' => Seed::generateCreatedDate(),
                        'updated_at' => Seed::generateUpdatedDate()
                    ]);
                });
            });
        });
    }
}