<?php

use Corcel\Post;

$factory->define(Corcel\ThumbnailMeta::class, function (Faker\Generator $faker) {
    return [
        'meta_key' => $faker->word,
        'meta_value' => $faker->sentence(),
        'post_id' => function () {
            return factory(Post::class)->create()->ID;
        },
    ];
});
