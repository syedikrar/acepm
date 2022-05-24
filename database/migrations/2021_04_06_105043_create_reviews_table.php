<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateReviewsTable.
 */
class CreateReviewsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reviews', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('reviewer_id')->unsigned();
            $table->integer('gig_id')->unsigned();
            $table->enum('ratings', [1,2,3,4,5])->default(5);
            $table->string('description');
            $table->timestamps();
		});

        Schema::table('reviews', function (Blueprint $table) {
            $table
                ->foreign('user_id', 'reviews_user_id_foreign_key')
                ->references('id')
                ->on('users')
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
		Schema::drop('reviews');
	}
}
