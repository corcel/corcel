<?php

namespace Corcel\Tests\Database\Migrations;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTermTaxonomyTable extends Migration
{
    /**
     * Run the Migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('term_taxonomy', function (Blueprint $table) {
            $table->increments('term_taxonomy_id');
            $table->bigInteger('term_id')->unsigned();
            $table->string('taxonomy');
            $table->longText('description');
            $table->bigInteger('parent')->unsigned();
            $table->bigInteger('count');
        });
    }

    /**
     * Reverse the Migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('term_taxonomy');
    }
}
