<?php

use Corcel\Model\Taxonomy;
use Corcel\Model\Term;

$factory->define(Taxonomy::class, function (Faker\Generator $faker) {
    return [
        'taxonomy' => $faker->word,
        'description' => $faker->sentence(),
        'parent' => 0,
        'count' => 1,
        'term_id' => function () {
            return factory(Term::class)->create([
                'name' => 'Bar',
                'slug' => 'bar',
            ])->term_id;
        },
    ];
});
