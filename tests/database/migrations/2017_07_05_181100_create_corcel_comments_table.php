<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorcelCommentsTable extends Migration
{
    /**
     * Run the Migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('comment_ID');
            $table->bigInteger('comment_post_ID')->unsigned()->default(0);
            $table->mediumText('comment_author');
            $table->string('comment_author_email');
            $table->string('comment_author_url');
            $table->string('comment_author_IP');
            $table->dateTime('comment_date')->default('0000-00-00 00:00:00');
            $table->dateTime('comment_date_gmt')->default('0000-00-00 00:00:00');
            $table->text('comment_content');
            $table->integer('comment_karma')->default(0);
            $table->string('comment_approved')->default(1);
            $table->string('comment_agent');
            $table->string('comment_type');
            $table->bigInteger('comment_parent')->unsigned()->default(0);
            $table->bigInteger('user_id')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the Migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
