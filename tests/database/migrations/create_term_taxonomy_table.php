<?php

use Illuminate\Database\Schema\Blueprint;

$capsule->schema()->create('term_taxonomy', function (Blueprint $table) {
    $table->increments('term_taxonomy_id');
    $table->bigInteger('term_id')->unsigned();
    $table->string('taxonomy');
    $table->longText('description');
    $table->bigInteger('parent')->unsigned();
    $table->bigInteger('count');
});
