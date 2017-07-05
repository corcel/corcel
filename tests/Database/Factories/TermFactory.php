<?php

use Illuminate\Support\Str;

$factory->define(Corcel\Term::class, function (Faker\Generator $faker) {
    return [
        'name' => $name = $faker->word,
        'slug' => Str::slug($name),
        'term_group' => 0,
    ];
});
