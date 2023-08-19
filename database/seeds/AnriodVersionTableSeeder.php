<?php

use Illuminate\Database\Seeder;
use App\Helpers\Seed;
use App\Models\AndriodVersion;

class AnriodVersionTableSeeder extends Seeder
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
            	'id' => '0c43ede3-8288-4bbf-9fa3-88dccd72187c',
                'latest_version_code' => 12,
                'require_force_update' => 0,
                'min_version_code' => 11,
                'play_store_link' => 'https://play.google.com/store/apps/details?id=ims.fojo.dw.communityradio',
            ]
        ];

        Seed::insertData(AndriodVersion::class, $rows);
    }
}
