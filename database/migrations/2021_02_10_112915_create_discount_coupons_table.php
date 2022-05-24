<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateDiscountCouponsTable.
 */
class CreateDiscountCouponsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('discount_coupons', function(Blueprint $table) {
            $table->increments('id');
            $table->text('shop_name')->nullable();
            $table->text('coupon')->nullable();
            $table->integer('percentage')->default(10)->nullable();
            $table->enum('status', ['requested', 'granted', 'availed', 'declined'])->nullable();
            $table->dateTime('requested_on')->nullable();
            $table->dateTime('availed_on')->nullable();
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
		Schema::drop('discount_coupons');
	}
}
