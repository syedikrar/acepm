<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCardAttachmentsTable.
 */
class CreateCardAttachmentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('card_attachments', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('card_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->longText('path');
            $table->string('name');
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
		Schema::drop('card_attachments');
	}
}
