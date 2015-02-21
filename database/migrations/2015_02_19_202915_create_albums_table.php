<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('albums', function(Blueprint $table)
		{
            $table->engine = 'InnoDB';

            // ID's, hashes and keys
			$table->increments('id');
            $table->char('sid', 7)->index()->unique();

            // Album information
            $table->string('name');
            $table->string('description');

            // User / uploader information
            $table->integer('user_id')->indexed()->default(0);     // image owner
            $table->string('upload_ip', 45)->nullable();           // uploader IP address
            $table->string('upload_uagent', 510)->nullable();      // uploader user agent

            // Timestamps
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('albums');
	}

}
