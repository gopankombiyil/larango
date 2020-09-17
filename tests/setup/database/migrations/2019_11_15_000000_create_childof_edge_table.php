<?php

use Illuminate\Database\Migrations\Migration;
use GopanKombiyil\Larango\Facades\Schema;
use GopanKombiyil\Larango\Schema\Blueprint;

class CreateChildOfEdgeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'child_of',
            function (Blueprint $collection) {
            },
            ['type' => 3]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('child_of');
    }
}
