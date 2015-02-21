<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColorschemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('colorschemes', function(Blueprint $table)
		{
			$table->increments('id');

			$table->string('name');                   // Name of the color scheme
			$table->tinyInteger('red')->unsigned();   // Red hue
			$table->tinyInteger('green')->unsigned(); // Green hue
			$table->tinyInteger('blue')->unsigned();  // Blue hue
			$table->char('hex', 7);                   // Hex representation of the RGB value
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('colorschemes');
	}

}
