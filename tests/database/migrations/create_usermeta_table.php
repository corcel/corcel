<?php

use Illuminate\Database\Schema\Blueprint;

$capsule->schema()->create('usermeta', function (Blueprint $table) {
    $table->increments('umeta_id');
    $table->bigInteger('user_id')->unsigned();
    $table->string('meta_key');
    $table->longText('meta_value');
});
