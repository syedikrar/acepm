<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGigPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gig_packages', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->text("details");
            $table->tinyInteger("delivery")->default(1);
            $table->tinyInteger("revisions")->default(1);
            $table->double("price");
            $table->foreignId("gig_id");
            $table->timestamps();

        });

        Schema::table('gig_packages', function (Blueprint $table) {
            $table
                ->foreign('gig_id', 'gig_packages_gig_id_foreign_key')
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
        Schema::dropIfExists('gig_packages');
    }
}
