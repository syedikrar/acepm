<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateTimeTrackersTable.
 */
class CreateTimeTrackersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('time_trackers', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('card_id')->unsigned();
            $table->text('description')->nullable();
            $table->string('time_spent')->nullable();
            $table->string('time_remaining')->nullable();
            $table->dateTime('date_started')->nullable();
            $table->timestamps();

            $table
                ->foreign('card_id', 'time_tracker_card_id_foreign_key')
                ->references('id')
                ->on('cards')
                ->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('time_trackers');
	}
}
