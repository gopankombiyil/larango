<?php

use Illuminate\Database\Migrations\Migration;
use GopanKombiyil\Larango\Facades\Schema;
use GopanKombiyil\Larango\Schema\Blueprint;

class CreateCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characters', function (Blueprint $collection) {
//            $collection->fulltextIndex('name');
//            $collection->fulltextIndex('surname');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('characters');
    }
}
