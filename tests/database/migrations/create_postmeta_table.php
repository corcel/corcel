<?php

use Illuminate\Database\Schema\Blueprint;

$capsule->schema()->create('postmeta', function (Blueprint $table) {
    $table->increments('meta_id');
    $table->bigInteger('post_id')->unsigned();
    $table->string('meta_key');
    $table->longText('meta_value');
});
