<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWatchHistoryTable extends Migration
{
    /**
     * تشغيل عملية الترحيل.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watch_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->morphs('watchable'); // للربط مع فيلم أو حلقة
            $table->integer('progress')->default(0); // نسبة المشاهدة (بالثواني)
            $table->integer('duration')->default(0); // المدة الكلية (بالثواني)
            $table->boolean('is_completed')->default(false);
            $table->timestamp('last_watched_at');
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
        Schema::dropIfExists('watch_history');
    }
}