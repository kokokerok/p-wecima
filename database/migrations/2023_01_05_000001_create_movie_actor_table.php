<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovieActorTable extends Migration
{
    /**
     * تشغيل عملية الترحيل.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movie_actor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('movie_id');
            $table->unsignedBigInteger('actor_id');
            $table->string('character_name')->nullable();
            $table->boolean('is_main_character')->default(false);
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
        Schema::dropIfExists('movie_actor');
    }
}