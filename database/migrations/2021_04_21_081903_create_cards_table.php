<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCardsTable.
 */
class CreateCardsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cards', function(Blueprint $table) {
            $table->id();
            $table->bigInteger('board_id')->unsigned();
            $table->bigInteger('assignee_id')->unsigned()->nullable();
            $table->bigInteger('pre_requisite_id')->unsigned()->nullable();
            $table->bigInteger('gig_id')->unsigned()->nullable();
            $table->integer('pane_index')->default(0);
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->string('label')->nullable();
            $table->string('tags')->nullable();
            $table->enum('priority', ['low', 'average', 'high', 'highest'])->default('average');

            $table->timestamps();

            $table
                ->foreign('board_id', 'card_board_id_foreign_key')
                ->references('id')
                ->on('boards')
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
		Schema::drop('cards');
	}
}
