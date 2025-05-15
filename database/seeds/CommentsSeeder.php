<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class CommentsSeeder extends Seeder
{
    /**
     * تشغيل عملية إضافة البيانات الافتراضية للتعليقات
     *
     * @return void
     */
    public function run()
    {
        // إنشاء مولد البيانات العشوائية
        $faker = Faker::create('ar_SA');
        
        // مسح البيانات السابقة (اختياري)
        // DB::table('comments')->truncate();

        // إضافة تعليقات للأفلام
        $movieComments = [];
        for ($i = 1; $i <= 50; $i++) {
            $movieComments[] = [
                'user_id' => rand(1, 20), // افتراض وجود 20 مستخدم
                'commentable_id' => rand(1, 30), // افتراض وجود 30 فيلم
                'commentable_type' => 'App\\Models\\Movie',
                'content' => $faker->realText(rand(50, 200)),
                'status' => $faker->randomElement(['approved', 'pending', 'spam']),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => Carbon::now(),
            ];
        }

        // إضافة تعليقات للمسلسلات
        $seriesComments = [];
        for ($i = 1; $i <= 50; $i++) {
            $seriesComments[] = [
                'user_id' => rand(1, 20), // افتراض وجود 20 مستخدم
                'commentable_id' => rand(1, 20), // افتراض وجود 20 مسلسل
                'commentable_type' => 'App\\Models\\Series',
                'content' => $faker->realText(rand(50, 200)),
                'status' => $faker->randomElement(['approved', 'pending', 'spam']),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => Carbon::now(),
            ];
        }

        // إضافة تعليقات للحلقات
        $episodeComments = [];
        for ($i = 1; $i <= 100; $i++) {
            $episodeComments[] = [
                'user_id' => rand(1, 20), // افتراض وجود 20 مستخدم
                'commentable_id' => rand(1, 100), // افتراض وجود 100 حلقة
                'commentable_type' => 'App\\Models\\Episode',
                'content' => $faker->realText(rand(50, 200)),
                'status' => $faker->randomElement(['approved', 'pending', 'spam']),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => Carbon::now(),
            ];
        }

        // دمج جميع التعليقات
        $allComments = array_merge($movieComments, $seriesComments, $episodeComments);

        // إدخال البيانات إلى قاعدة البيانات
        DB::table('comments')->insert($allComments);

        $this->command->info('تم إضافة بيانات التعليقات بنجاح!');
    }
}