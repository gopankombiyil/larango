<?php

use Illuminate\Database\Migrations\Migration;
use GopanKombiyil\Larango\Facades\Schema;
use GopanKombiyil\Larango\Schema\Blueprint;

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
