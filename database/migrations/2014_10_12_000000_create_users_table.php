<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('oauth_id')->unsigned()->index()->default(0); // OAuth user ID
            $table->string('oauth_driver')->nullable();        // OAuth driver
			$table->string('name')->unique();                  // Display name
			$table->string('email')->unique();                 // E-mail address / primary login
			$table->char('password', 60);                      // Password
			$table->boolean('is_admin')->default(0);           // User is an administrator
			$table->boolean('is_moderator')->default(0);       // User is a moderator
            $table->boolean('active')->default(0);             // User is activated
            $table->char('activation_code', 40)->nullable();   // E-Mail activation code
            $table->char('activation_token', 100)->nullable(); // Used to safely log the user in after activation
			$table->rememberToken();
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
		Schema::drop('users');
	}

}
