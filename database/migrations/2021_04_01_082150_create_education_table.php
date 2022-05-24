<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('education', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->string('country');
            $table->string('university');
            $table->enum('title', ['Associate', 'Certificate', 'B.A', 'BArch', 'BM', 'BFA', 'B.Sc', 'M.A.', 'M.B.A', 'MFA', 'M.Sc', 'J.D.', 'M.D.', 'Ph.D', 'LLB', 'LLM', 'Other']);
            $table->string('major');
            $table->year('year');
            $table->timestamps();

            $table
                ->foreign('user_id', 'education_user_id_foreign_key')
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
        Schema::dropIfExists('education');
    }
}
