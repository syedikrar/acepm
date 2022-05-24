<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_user', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('board_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->enum('role', ['creator', 'member'])->default('member');

            $table->foreign('board_id', 'board_user_board_id_foreign_key')
                ->references('id')
                ->on('boards')
                ->onDelete('cascade');

            $table->foreign('user_id', 'board_user_user_id_foreign_key')
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
        Schema::dropIfExists('board_user');
    }
}
