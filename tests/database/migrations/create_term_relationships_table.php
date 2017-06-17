<?php

use Illuminate\Database\Schema\Blueprint;

$capsule->schema($connection)
    ->create('term_relationships', function (Blueprint $table) {
        $table->bigInteger('object_id')->unsigned();
        $table->bigInteger('term_taxonomy_id');
        $table->integer('term_order');
    });
