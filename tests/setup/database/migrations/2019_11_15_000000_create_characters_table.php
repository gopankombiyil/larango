<?php

use Illuminate\Database\Migrations\Migration;
use Gopankombiyil\Larango\Facades\Schema;
use Gopankombiyil\Larango\Schema\Blueprint;

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
