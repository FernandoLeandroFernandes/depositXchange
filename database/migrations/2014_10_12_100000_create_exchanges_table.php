<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('exchanges', function (Blueprint $table) {
			
			$table->integer('origin_id');
			$table->integer('destination_id');
			$table->decimal('amount');
			$table->timestamps();

			$table->foreign('origin_id')->references('id')->on('banks');
			$table->foreign('destination_id')->references('id')->on('banks');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('exchanges');
	}
}
