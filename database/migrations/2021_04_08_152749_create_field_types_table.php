<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCategoriesTable.
 */
class CreateFieldTypesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('field_types', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique('field_slug');
            $table->string('icon');
            $table->string('color')->nullable();
            $table->boolean('enabled')->default(true);
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
		Schema::drop('field_types');
	}
}
