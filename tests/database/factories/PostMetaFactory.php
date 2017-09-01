<?php

use Corcel\Model\Meta\PostMeta;

$factory->define(PostMeta::class, function (Faker\Generator $faker) {
    return [
        'post_id' => $faker->numberBetween(1, 100),
        'meta_key' => $faker->word,
        'meta_value' => $faker->sentence(),
    ];
});
