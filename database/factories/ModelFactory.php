<?php

use App\Concert;
use Carbon\Carbon;

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Concert::class, function (Faker\Generator $faker) {
    return [
        'title' => 'Example and',
        'subtitle' => 'With love Fake Opener',
        'date' => Carbon::parse('+2 weeks'),
        'ticket_price' => 2000,
        'venue' => 'The Helsingor Kommune 99',
        'venue_address' => 'Nordvej 19, 1tv',
        'city' => 'FakeHeslingor',
        'state' => 'H',
        'zip' => '4000',
        'addition_information' => 'Call-92165545, XXX',
    ];
});

$factory->state(App\Concert::class, 'published', function ($faker) {
    return [
        'published_at' => Carbon::parse('-1 week'),
    ];
});

$factory->state(App\Concert::class, 'unpublished', function ($faker) {
    return [
        'published_at' => null,
    ];
});

$factory->define(App\Ticket::class, function (Faker\Generator $faker) {
    return [
        //'concert_id' => factory(App\Concert::class)->create()->id,

        'concert_id' => function() {
            return factory(App\Concert::class)->create()->id;
        },
    ];

});