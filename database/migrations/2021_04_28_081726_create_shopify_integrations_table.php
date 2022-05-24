<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateShopifyIntegrationsTable.
 */
class CreateShopifyIntegrationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shopify_integrations', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('card_id')->unsigned();
            $table->enum('resource_type', ['product', 'order'])->default('product');
            $table->bigInteger('resource_id')->unsigned();
            $table->timestamps();

            $table->foreign('card_id', 'card_id_foreign_key')->references('id')->on('cards')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shopify_integrations');
	}
}
