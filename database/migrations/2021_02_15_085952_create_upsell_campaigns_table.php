<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUpsellCampaignsTable.
 */
class CreateUpsellCampaignsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('upsell_campaigns', function(Blueprint $table) {
            $table->increments('id');
            $table->string('plan')
                ->nullable();
            $table->enum('type', ['upsell', 'review'])->default('upsell');
            $table->text('title');
            $table->longText('message');
            $table->integer('repeat_after')->default(1);
            $table->integer('max_tries')->nullable();
            $table->dateTime('campaign_starts')->nullable();
            $table->dateTime('campaign_expires')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('impressions')->default(0);
            $table->integer('conversions')->default(0);
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
		Schema::drop('upsell_campaigns');
	}
}
