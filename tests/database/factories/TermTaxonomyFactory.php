<?php

$factory->define(Corcel\TermTaxonomy::class, function (Faker\Generator $faker) {
    return [
        'taxonomy' => $faker->word,
        'description' => $faker->sentence(),
        'parent' => 0,
        'count' => 1,
        'term_id' => function () {
            return factory(\Corcel\Term::class)->create([
                'name' => 'Bar',
                'slug' => 'bar',
            ])->term_id;
        },
    ];
});
