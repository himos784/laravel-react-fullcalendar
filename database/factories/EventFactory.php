<?php

use App\Event;
use Faker\Generator as Faker;

$factory->define(Event::class, function (Faker $faker) {
    return [
        'event' => $faker->name,
        'schedule_start' => null,
        'schedule_end' => null,
    ];
});
