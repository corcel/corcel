<?php

use Illuminate\Database\Schema\Blueprint;

$capsule->schema($connection)
    ->create('commentmeta', function (Blueprint $table) {
        $table->increments('meta_id');
        $table->bigInteger('comment_id')->unsigned();
        $table->string('meta_key');
        $table->longText('meta_value');
    });
