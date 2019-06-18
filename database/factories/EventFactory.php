<?php

use App\Event;
use Faker\Generator as Faker;

$factory->define(Event::class, function (Faker $faker) {
    return [
        'event'    => $faker->name,
        'schedule' => null,
    ];
});