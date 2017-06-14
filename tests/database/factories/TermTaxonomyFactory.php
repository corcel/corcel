<?php

$factory->define(Corcel\TermTaxonomy::class, function (Faker\Generator $faker) {
    return [
        'taxonomy' => $faker->word,
        'description' => $faker->sentence(),
        'parent' => 0,
        'count' => 1,
    ];
});
