<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGigQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gig_questions', function (Blueprint $table) {
            $table->id();
            $table->text("question");
            $table->tinyInteger("required")->default(0);
            $table->foreignId("gig_id");
            $table->timestamps();

        });

        Schema::table('gig_questions', function (Blueprint $table) {
            $table
                ->foreign('gig_id', 'gig_questions_gig_id_foreign_key')
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
        Schema::dropIfExists('gig_questions');
    }
}
