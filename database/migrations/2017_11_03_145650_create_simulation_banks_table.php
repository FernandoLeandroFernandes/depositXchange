<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSimulationBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('simulation_banks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('simulation_id');
            $table->integer('bank_id');
            $table->timestamps();

			$table->foreign('simulation_id')->references('id')->on('simulations');
            $table->foreign('bank_id')->references('id')->on('banks');
            $table->unique(['simulation_id', 'bank_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('simulation_banks');
    }
}
