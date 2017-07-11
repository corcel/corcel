<?php

use Corcel\Model\Meta\CommentMeta;

$factory->define(CommentMeta::class, function (Faker\Generator $faker) {
    return [
        'comment_id' => $faker->numberBetween(1, 100),
        'meta_key' => $faker->word,
        'meta_value' => $faker->sentence(),
    ];
});
