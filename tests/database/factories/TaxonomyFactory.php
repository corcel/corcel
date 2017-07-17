<?php

use Corcel\Model\Taxonomy;
use Corcel\Model\Term;

$factory->define(Taxonomy::class, function (Faker\Generator $faker) {
    $term = factory(Term::class)->create([
        'name' => 'Bar',
        'slug' => 'bar',
    ]);

    return [
        'taxonomy' => $faker->word,
        'description' => $faker->sentence(),
        'parent' => 0,
        'count' => 1,
        'term_id' => $term->term_id,
    ];
});
