<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_user', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('shop_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->enum('role', ['owner', 'admin', 'staff'])->default('staff');

            $table->foreign('shop_id', 'shop_user_shop_id_foreign_key')
                ->references('id')
                ->on('shops')
                ->onDelete('cascade');

            $table->foreign('user_id', 'shop_user_user_id_foreign_key')
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
        Schema::dropIfExists('shop_user');
    }
}
