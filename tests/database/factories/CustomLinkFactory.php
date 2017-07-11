<?php

use Corcel\Model\CustomLink;
use Illuminate\Support\Str;

$factory->define(CustomLink::class, function (Faker\Generator $faker) {
    return [
        'post_author' => $faker->name,
        'post_date' => $faker->dateTimeThisYear,
        'post_date_gmt' => $faker->dateTimeThisYear,
        'post_content' => $faker->text(),
        'post_title' => $title = $faker->title,
        'post_excerpt' => $faker->text(100),
        'post_status' => 'publish',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_password' => '',
        'post_name' => Str::slug($title),
        'to_ping' => '',
        'pinged' => '',
        'post_modified' => $faker->dateTimeThisMonth,
        'post_modified_gmt' => $faker->dateTimeThisMonth,
        'post_content_filtered' => '',
        'post_parent' => 0,
        'guid' => 'http://example.com/?p=' . $faker->numberBetween(1, 100),
        'menu_order' => 0,
        'post_type' => 'nav_menu_item',
        'post_mime_type' => '',
        'comment_count' => 0,
    ];
});
