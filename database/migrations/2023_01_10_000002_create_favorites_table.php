<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoritesTable extends Migration
{
    /**
     * تنفيذ الترحيل.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->morphs('favorable'); // لتخزين نوع المحتوى (فيلم أو مسلسل) ومعرفه
            $table->timestamps();
            
            // منع تكرار الإضافة للمفضلة
            $table->unique(['user_id', 'favorable_id', 'favorable_type']);
            
            // مؤشر للبحث السريع
            $table->index('user_id');
        });
    }

    /**
     * التراجع عن الترحيل.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('favorites');
    }
}