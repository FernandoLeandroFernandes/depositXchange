<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBanksTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('banks', function (Blueprint $table) {

			$table->integer('id')->primary();
			$table->string('name');
			$table->string('city');
			$table->decimal('max_amount', 10, 2)->nullable();
			$table->integer('max_connections')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('banks');
	}
}
