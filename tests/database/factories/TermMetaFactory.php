<?php

use Corcel\Model\Meta\TermMeta;

$factory->define(TermMeta::class, function (Faker\Generator $faker) {
    return [
        'term_id' => function () {
            return factory(\Corcel\Model\Term::class)->create()->term_id;
        },
        'meta_key' => $faker->word,
        'meta_value' => $faker->sentence(),
    ];
});
