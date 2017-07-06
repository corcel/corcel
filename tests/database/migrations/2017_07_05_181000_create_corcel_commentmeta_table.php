<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorcelCommentmetaTable extends Migration
{
    /**
     * Run the Migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commentmeta', function (Blueprint $table) {
            $table->increments('meta_id');
            $table->bigInteger('comment_id')->unsigned();
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
        Schema::dropIfExists('commentmeta');
    }
}
