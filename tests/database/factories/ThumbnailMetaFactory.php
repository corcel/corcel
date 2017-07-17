<?php

use Corcel\Model\Meta\ThumbnailMeta;
use Corcel\Model\Post;

$factory->define(ThumbnailMeta::class, function (Faker\Generator $faker) {
    $post = factory(Post::class)->create();

    return [
        'meta_key' => '_thumbnail_id',
        'meta_value' => $faker->sentence(),
        'post_id' => $post->ID,
    ];
});
