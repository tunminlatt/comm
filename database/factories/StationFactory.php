<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Station;
use Faker\Generator as Faker;

$factory->define(Station::class, function (Faker $faker) {
    return [
        'title' => $faker->unique()->word,
        'description' => $faker->sentence(5),
        'email' => $faker->email,
        'phone' => str_replace('+', '', $faker->e164PhoneNumber),
        'facebook_link' => $faker->url,
    ];
});