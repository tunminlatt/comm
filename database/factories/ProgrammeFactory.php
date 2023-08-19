<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Programme;
use Faker\Generator as Faker;

$factory->define(Programme::class, function (Faker $faker) {
    return [
        'title' => $faker->unique()->sentence(5),
        'duration' => $faker->time('i:s'),
        'description' => $faker->sentence(5),
    ];
});