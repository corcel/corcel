<?php

use Corcel\Model\Comment;
use Corcel\Model\Post;

$factory->define(Comment::class, function (Faker\Generator $faker) {
    return [
        'comment_post_ID' => function () {
            return factory(Post::class)->create()->ID;
        },
        'comment_author' => $faker->name,
        'comment_author_email' => $faker->email,
        'comment_author_url' => $faker->url,
        'comment_author_IP' => $faker->ipv4,
        'comment_date' => $faker->dateTime,
        'comment_date_gmt' => $faker->dateTime,
        'comment_content' => $faker->sentence(),
        'comment_karma' => 0,
        'comment_approved' => 1,
        'comment_agent' => '',
        'comment_type' => '',
        'comment_parent' => 0,
        'user_id' => 0,
    ];
});
