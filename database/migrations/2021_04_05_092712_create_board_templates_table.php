<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateBoardTemplatesTable.
 */
class CreateBoardTemplatesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('board_templates', function(Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('field_type_id')->unsigned();
            $table->string('name');
            $table->string('description', 500);
            $table->json('content');
            $table->dateTime('approved_at')->nullable();
            $table->bigInteger('views')->default(0);
            $table->bigInteger('installs')->default(0);
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
		Schema::drop('board_templates');
	}
}
