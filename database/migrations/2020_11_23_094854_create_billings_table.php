<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateBillingsTable.
 */
class CreateBillingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('billings', function(Blueprint $table) {
            $table->id();
            $table->bigInteger('shop_id')->unsigned();
            $table->string('shop_domain');
            $table->bigInteger('shopify_billing_id')->unsigned();
            $table->string('plan');
            $table->float('price')->nullable();
            $table->enum('type', ['app', 'feature'])->default('app');
            $table->enum('status', ['pending', 'accepted', 'declined', 'active', 'expired', 'frozen', 'cancelled']);
            $table->dateTime('activated_on')->nullable();
            $table->integer('trial_days')->nullable();
            $table->dateTime('billing_on')->nullable();
            $table->boolean('refunded')->default(false);

            $table->timestamps();
            $table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('billings');
	}
}
