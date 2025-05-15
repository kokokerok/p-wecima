<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class RatingsSeeder extends Seeder
{
    /**
     * تشغيل عملية إضافة البيانات الافتراضية للتقييمات
     *
     * @return void
     */
    public function run()
    {
        // إنشاء مولد البيانات العشوائية
        $faker = Faker::create();
        
        // مسح البيانات السابقة (اختياري)
        // DB::table('ratings')->truncate();

        // إضافة تقييمات للأفلام
        $movieRatings = [];
        for ($i = 1; $i <= 200; $i++) {
            $movieRatings[] = [
                'user_id' => rand(1, 20), // افتراض وجود 20 مستخدم
                'ratable_id' => rand(1, 30), // افتراض وجود 30 فيلم
                'ratable_type' => 'App\\Models\\Movie',
                'rating' => $faker->randomFloat(1, 1, 10),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => Carbon::now(),
            ];
        }

        // إضافة تقييمات للمسلسلات
        $seriesRatings = [];
        for ($i = 1; $i <= 150; $i++) {
            $seriesRatings[] = [
                'user_id' => rand(1, 20), // افتراض وجود 20 مستخدم
                'ratable_id' => rand(1, 20), // افتراض وجود 20 مسلسل
                'ratable_type' => 'App\\Models\\Series',
                'rating' => $faker->randomFloat(1, 1, 10),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => Carbon::now(),
            ];
        }

        // دمج جميع التقييمات
        $allRatings = array_merge($movieRatings, $seriesRatings);

        // إدخال البيانات إلى قاعدة البيانات
        DB::table('ratings')->insert($allRatings);

        $this->command->info('تم إضافة بيانات التقييمات بنجاح!');
    }
}