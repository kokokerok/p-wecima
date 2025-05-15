<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEpisodesTable extends Migration
{
    /**
     * تشغيل عملية الترحيل.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('season_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('episode_number');
            $table->string('video_url')->nullable();
            $table->integer('duration')->nullable(); // بالدقائق
            $table->string('quality')->nullable();
            $table->enum('status', ['published', 'draft', 'pending'])->default('published');
            $table->integer('views')->default(0);
            $table->integer('downloads')->default(0);
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
        Schema::dropIfExists('episodes');
    }
}