<?php

use Corcel\Model\Menu;
use Corcel\Model\Term;

$factory->define(Menu::class, function (Faker\Generator $faker) {
    $term = factory(Term::class)->create([
        'name' => 'Foo',
        'slug' => 'foo',
    ]);

    return [
        'taxonomy' => 'nav_menu',
        'description' => $faker->sentence(),
        'parent' => 0,
        'count' => 1,
        'term_id' => $term->term_id,
    ];
});
