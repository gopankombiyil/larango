<?php

use Illuminate\Database\Migrations\Migration;
use Gopankombiyil\Larango\Facades\Schema;
use Gopankombiyil\Larango\Schema\Blueprint;

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
