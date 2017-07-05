<?php

use Illuminate\Database\Schema\Blueprint;

$capsule->schema($connection)
    ->create('terms', function (Blueprint $table) {
        $table->increments('term_id');
        $table->string('name');
        $table->string('slug');
        $table->bigInteger('term_group');
    });
