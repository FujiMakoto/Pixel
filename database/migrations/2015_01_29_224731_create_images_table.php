<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('images', function($table)
		{

			$table->engine = 'InnoDB';

			// ID's, hashes and keys
			$table->increments('id');                              // primary identifier
			$table->char('sid', 7)->index()->unique();             // string identifier
			$table->char('md5sum', 32);                            // md5sum of the image file
			$table->char('delete_key', 40)->nullable();            // key used to allow guest deletion of images

			// Image information
			$table->integer('album_id')->unsigned()->default(0);   // image album
			$table->string('name', 510)->default('Untitled');      // the original un-formatted file name
			$table->string('type', 4);                             // image type/extension
			$table->integer('size')->unsigned();                   // image size
			$table->integer('views')->unsigned();                  // image views
			$table->smallInteger('width')->unsigned();             // image width
			$table->smallInteger('height')->unsigned();            // image height
			$table->smallInteger('original_width')->unsigned();    // original image width
			$table->smallInteger('original_height')->unsigned();   // original image height

			// User / uploader information
			$table->integer('user_id')->indexed()->default(0);     // image owner
			$table->string('upload_ip', 45)->nullable();           // uploader IP address
			$table->string('upload_uagent', 510)->nullable();      // uploader user agent

			// Dominant color RGB values, used for color math
			$table->tinyInteger('red')->unsigned()->nullable();    // red
			$table->tinyInteger('green')->unsigned()->nullable();  // green
			$table->tinyInteger('blue')->unsigned()->nullable();   // blue

			// Timestamps
			$table->timestamps();                                  // standard timestamps
			$table->timestamp('expires')->nullable();              // image expiration date (for temp uploads)
			$table->softDeletes();                                 // used for "un-approving"/hiding images

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('images');
	}

}
