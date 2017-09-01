<?php

use Corcel\Model\Menu;
use Corcel\Model\Term;

$factory->define(Menu::class, function (Faker\Generator $faker) {
    return [
        'taxonomy' => 'nav_menu',
        'description' => $faker->sentence(),
        'parent' => 0,
        'count' => 1,
        'term_id' => function () {
            return factory(Term::class)->create([
                'name' => 'Foo',
                'slug' => 'foo',
            ])->term_id;
        },
    ];
});
