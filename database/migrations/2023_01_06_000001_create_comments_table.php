<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * تشغيل عملية الترحيل.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->morphs('commentable'); // للتعليق على أفلام أو مسلسلات أو حلقات
            $table->text('content');
            $table->unsignedBigInteger('parent_id')->nullable(); // للردود على التعليقات
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_spoiler')->default(false);
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
        Schema::dropIfExists('comments');
    }
}