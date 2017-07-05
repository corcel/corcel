<?php

use Illuminate\Support\Str;

$factory->define(Corcel\Option::class, function (Faker\Generator $faker) {
    return [
        'option_name' => $faker->word,
        'option_value' => $faker->sentence(),
        'autoload' => 'yes',
    ];
});
