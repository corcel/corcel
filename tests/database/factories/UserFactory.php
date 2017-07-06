<?php

$factory->define(\Corcel\User::class, function (Faker\Generator $faker) {
    return [
        'user_login' => 'admin',
        'user_pass' => 'secret',
        'user_nicename' => 'admin',
        'user_email' => 'admin@example.com',
        'user_url' => 'http://admin.example.com',
        'user_registered' => $faker->dateTime,
        'user_activation_key' => str_random(10),
        'user_status' => 0,
        'display_name' => 'Administrator',
    ];
});
