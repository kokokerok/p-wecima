<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesTable extends Migration
{
    /**
     * تشغيل عملية الترحيل.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('poster')->nullable();
            $table->string('banner')->nullable();
            $table->string('trailer_url')->nullable();
            $table->string('video_url')->nullable();
            $table->integer('year')->nullable();
            $table->integer('duration')->nullable(); // بالدقائق
            $table->string('quality')->nullable();
            $table->string('country')->nullable();
            $table->string('language')->nullable();
            $table->decimal('imdb_rating', 3, 1)->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_premium')->default(false);
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
        Schema::dropIfExists('movies');
    }
}