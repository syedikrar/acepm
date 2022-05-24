<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateSubTasksTable.
 */
class CreateSubTasksTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sub_tasks', function(Blueprint $table) {
            $table->id();
            $table->bigInteger('card_id')->unsigned();
            $table->string('title');
            $table->dateTime('due_date')->nullable();
            $table->boolean('done')->default(false);
            $table->timestamps();

            $table
                ->foreign('card_id', 'sub_task_card_id_foreign_key')
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
		Schema::drop('sub_tasks');
	}
}
