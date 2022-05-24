<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId("gig_id");
            $table->foreignId("package_id");
            $table->integer("user_id")->unsigned();
            $table->text("answers")->nullable();
            $table->timestamps();
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table
                ->foreign('gig_id', 'contracts_gig_id_foreign_key')
                ->references('id')
                ->on('gigs')
                ->onDelete('cascade');

            $table
                ->foreign('package_id', 'contracts_package_id_foreign_key')
                ->references('id')
                ->on('gig_packages')
                ->onDelete('cascade');

            $table
                ->foreign('user_id', 'contracts_user_id_foreign_key')
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
        Schema::dropIfExists('contracts');
    }
}
