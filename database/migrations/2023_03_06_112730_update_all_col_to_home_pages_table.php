<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAllColToHomePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('home_pages', function (Blueprint $table) {
            //
            $table->longText('banner')->change();
            $table->longText('about_us')->change();
            $table->longText('services')->change();
            $table->longText('projects')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('home_pages', function (Blueprint $table) {
            //
            $table->string('banner')->change();
            $table->string('about_us')->change();
            $table->string('services')->change();
            $table->string('projects')->change();
        });
    }
}
