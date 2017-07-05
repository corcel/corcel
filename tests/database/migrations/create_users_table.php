<?php

use Illuminate\Database\Schema\Blueprint;

$capsule->schema($connection)
    ->create('users', function (Blueprint $table) {
        $table->increments('ID');
        $table->string('user_login');
        $table->string('user_pass');
        $table->string('user_nicename');
        $table->string('user_email');
        $table->string('user_url');
        $table->string('user_registered');
        $table->string('user_activation_key');
        $table->string('user_status');
        $table->string('display_name');
    });
