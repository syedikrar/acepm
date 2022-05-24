<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gigs', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->foreignId("category_id");
            $table->foreignId("sub_category_id");
            $table->string("search_terms")->nullable();
            $table->longText("descriptions");
            $table->integer("user_id")->unsigned();
            $table->tinyInteger("status")->default(1);
            $table->timestamps();

        });

        Schema::table('gigs', function (Blueprint $table) {
            $table
                ->foreign('category_id', 'gigs_category_id_foreign_key')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            $table
                ->foreign('sub_category_id', 'gigs_sub_category_id_foreign_key')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            $table
                ->foreign('user_id', 'gigs_user_id_foreign_key')
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
        Schema::dropIfExists('gigs');
    }
}
