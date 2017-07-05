<?php

use Corcel\Post;

$factory->define(Corcel\ThumbnailMeta::class, function (Faker\Generator $faker) {
    return [
        'meta_key' => '_thumbnail_id',
        'meta_value' => $faker->sentence(),
        'post_id' => function () {
            return factory(Post::class)->create()->ID;
        },
    ];
});
