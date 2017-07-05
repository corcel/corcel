<?php

namespace Corcel\Tests\Database\Migrations;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostmetaTable extends Migration
{
    /**
     * Run the Migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postmeta', function (Blueprint $table) {
            $table->increments('meta_id');
            $table->bigInteger('post_id')->unsigned();
            $table->string('meta_key');
            $table->longText('meta_value');
        });
    }

    /**
     * Reverse the Migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('postmeta');
    }
}
