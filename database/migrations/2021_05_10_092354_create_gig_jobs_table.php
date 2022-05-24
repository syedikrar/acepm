<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGigJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gig_jobs', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id")->unsigned();
            $table->text("descriptions");
            $table->string("attachment")->nullable();
            $table->foreignId("category_id");
            $table->foreignId("sub_category_id");
            $table->tinyInteger("delivery")->default(1);
            $table->double("price");
            $table->foreignId("card_id");
            $table->timestamps();
        });

        Schema::table('gig_jobs', function (Blueprint $table) {
            $table
                ->foreign('user_id', 'gig_jobs_user_id_foreign_key')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table
                ->foreign('category_id', 'gig_jobs_category_id_foreign_key')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            $table
                ->foreign('sub_category_id', 'gig_jobs_sub_category_id_foreign_key')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            $table
                ->foreign('card_id', 'gig_jobs_sub_card_id_foreign_key')
                ->references('id')
                ->on('cards')
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
        Schema::dropIfExists('gig_jobs');
    }
}
