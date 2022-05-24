<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCommentsTable.
 */
class CreateCommentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comments', function(Blueprint $table) {
            $table->id();
            $table->bigInteger('card_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->text('body');

            $table->timestamps();

            $table
                ->foreign('card_id', 'comment_card_id_foreign_key')
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
		Schema::drop('comments');
	}
}
