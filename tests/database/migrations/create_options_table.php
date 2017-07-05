<?php

use Illuminate\Database\Schema\Blueprint;

$capsule->schema($connection)
    ->create('Option', function (Blueprint $table) {
        $table->increments('option_id');
        $table->string('option_name');
        $table->longText('option_value');
        $table->string('autoload')->default('yes');
    });
