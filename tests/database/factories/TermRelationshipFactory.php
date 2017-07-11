<?php

use Corcel\Model\TermRelationship;

$factory->define(TermRelationship::class, function (Faker\Generator $faker) {
    return [
        'term_order' => 0,
    ];
});
