<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBackgroundImageColumnsToBaordsAndTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->string('background_image')->default('')->after('name');
        });

        Schema::table('board_templates', function (Blueprint $table) {
            $table->string('background_image')->default('')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->dropColumn('background_image');
        });

        Schema::table('board_templates', function (Blueprint $table) {
            $table->dropColumn('background_image');
        });
    }
}
