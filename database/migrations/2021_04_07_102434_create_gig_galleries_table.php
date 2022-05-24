<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGigGalleriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gig_galleries', function (Blueprint $table) {
            $table->id();
            $table->string("image");
            $table->foreignId("gig_id");
            $table->timestamps();

        });

        Schema::table('gig_galleries', function (Blueprint $table) {
            $table
                ->foreign('gig_id', 'gig_galleries_gig_id_foreign_key')
                ->references('id')
                ->on('gigs')
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
        Schema::dropIfExists('gig_galleries');
    }
}
