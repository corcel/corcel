<?php

use Illuminate\Database\Schema\Blueprint;

$capsule->schema($connection)
    ->create('termmeta', function (Blueprint $table) {
        $table->increments('meta_id');
        $table->bigInteger('term_id')->unsigned();
        $table->string('meta_key');
        $table->longText('meta_value');
    });
