<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeriesDirectorTable extends Migration
{
    /**
     * تشغيل عملية الترحيل.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('series_director', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('series_id');
            $table->unsignedBigInteger('director_id');
            $table->timestamps();
        });
    }

    /**
     * التراجع عن عملية الترحيل.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('series_director');
    }
}