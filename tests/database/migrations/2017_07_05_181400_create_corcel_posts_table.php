<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorcelPostsTable extends Migration
{
    /**
     * Run the Migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('ID');
            $table->bigInteger('post_author')->unsigned();
            $table->dateTime('post_date');
            $table->dateTime('post_date_gmt');
            $table->longText('post_content');
            $table->text('post_title');
            $table->text('post_excerpt');
            $table->string('post_status');
            $table->string('comment_status');
            $table->string('ping_status');
            $table->string('post_password');
            $table->string('post_name');
            $table->text('to_ping');
            $table->text('pinged');
            $table->dateTime('post_modified');
            $table->dateTime('post_modified_gmt');
            $table->longText('post_content_filtered');
            $table->bigInteger('post_parent')->unsigned();
            $table->string('guid');
            $table->integer('menu_order');
            $table->string('post_type');
            $table->string('post_mime_type');
            $table->bigInteger('comment_count');
        });
    }

    /**
     * Reverse the Migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
