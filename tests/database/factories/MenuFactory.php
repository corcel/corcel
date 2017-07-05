<?php

$factory->define(\Corcel\Menu::class, function (Faker\Generator $faker) {
    return [
        'taxonomy' => 'nav_menu',
        'description' => $faker->sentence(),
        'parent' => 0,
        'count' => 1,
        'term_id' => function () {
            return factory(\Corcel\Term::class)->create([
                'name' => 'Foo',
                'slug' => 'foo',
            ])->term_id;
        },
    ];
});
