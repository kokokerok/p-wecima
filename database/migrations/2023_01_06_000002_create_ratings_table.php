<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    /**
     * تشغيل عملية الترحيل.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->morphs('rateable'); // للتقييم على أفلام أو مسلسلات أو حلقات
            $table->integer('rating'); // من 1 إلى 10
            $table->text('review')->nullable();
            $table->timestamps();
            
            // ضمان أن المستخدم يمكنه تقييم العنصر مرة واحدة فقط
            $table->unique(['user_id', 'rateable_id', 'rateable_type']);
        });
    }

    /**
     * التراجع عن عملية الترحيل.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}