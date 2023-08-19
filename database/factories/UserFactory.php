<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Helpers\Seed;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->email,
        'email_verified_at' => Seed::generateCurrentDate(),
        'password' => Seed::getDefaultPassword(),
        'remember_token' => Seed::generateRememberToken()
    ];
});