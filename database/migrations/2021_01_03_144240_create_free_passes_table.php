<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateFreePassesTable.
 */
class CreateFreePassesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('free_passes', function(Blueprint $table) {
            $table->id('id');
            $table->string('shop')->unique();
            $table->boolean('active')->default(true);
            $table->dateTime('last_checked')->nullable();
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
		Schema::drop('free_passes');
	}
}
