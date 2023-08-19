<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Audio;
use Faker\Generator as Faker;

$factory->define(Audio::class, function (Faker $faker) {
    return [
        'title' => $faker->unique()->sentence(5),
        'duration' => $faker->time('i:s'),
        'note' => $faker->sentence(5),
    ];
});