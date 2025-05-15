<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * تنفيذ الترحيل.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data'); // بيانات الإشعار بتنسيق JSON
            $table->timestamp('read_at')->nullable(); // وقت قراءة الإشعار
            $table->timestamps();
            
            // مؤشرات للبحث السريع
            $table->index('notifiable_id');
            $table->index('notifiable_type');
            $table->index('read_at');
        });
    }

    /**
     * التراجع عن الترحيل.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}