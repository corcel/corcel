<?php

namespace Corcel\Tests\Database\Migrations;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermRelationshipsTable extends Migration
{
    /**
     * Run the Migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('term_relationships', function (Blueprint $table) {
            $table->bigInteger('object_id')->unsigned();
            $table->bigInteger('term_taxonomy_id');
            $table->integer('term_order')->default(0);
        });
    }

    /**
     * Reverse the Migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('term_relationships');
    }
}
