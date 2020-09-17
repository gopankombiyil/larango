<?php

use Illuminate\Database\Migrations\Migration;
use GopanKombiyil\Larango\Facades\Schema;
use GopanKombiyil\Larango\Schema\Blueprint;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $collection) {
//            $collection->skiplistIndex('name');
//            $collection->geoIndex('coordinate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('locations');
    }
}
