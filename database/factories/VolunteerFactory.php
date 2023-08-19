<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Helpers\Seed;
use App\Models\Volunteer;
use Faker\Generator as Faker;

$factory->define(Volunteer::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'phone' => str_replace('+', '', $faker->e164PhoneNumber),
        'email' => $faker->email,
        'password' => Seed::getDefaultPassword(),
        'address' => $faker->address
    ];
});