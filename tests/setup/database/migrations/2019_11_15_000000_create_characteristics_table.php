<?php

use Illuminate\Database\Migrations\Migration;
use Gopankombiyil\Larango\Facades\Schema;
use Gopankombiyil\Larango\Schema\Blueprint;

class CreateCharacteristicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characteristics', function (Blueprint $collection) {
//            $collection->skiplistIndex('en[*]');
//            $collection->skiplistIndex('de[*]');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('characteristics');
    }
}
