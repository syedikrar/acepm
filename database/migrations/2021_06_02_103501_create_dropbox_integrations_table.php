<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateDropboxIntegrationsTable.
 */
class CreateDropboxIntegrationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dropbox_integrations', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('card_id');
            $table->string('file_id');
            $table->string('name');
            $table->string('url');
            $table->longText('icon_url');
            $table->timestamps();
		});

        Schema::table('dropbox_integrations', function($table){
            $table->foreign('card_id', 'dropboxcard_id_foreign')->references('id')->on('cards');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('dropbox_integrations');
	}
}
