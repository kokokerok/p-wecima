<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EpisodesSeeder extends Seeder
{
    /**
     * تشغيل عملية إضافة البيانات الافتراضية لحلقات المسلسلات
     *
     * @return void
     */
    public function run()
    {
        // مسح البيانات السابقة (اختياري)
        // DB::table('episodes')->truncate();

        // إضافة بيانات الحلقات للموسم الأول من مسلسل الاختيار
        $episodes = [];
        
        // مسلسل الاختيار - الموسم الأول (10 حلقات)
        for ($i = 1; $i <= 10; $i++) {
            $episodes[] = [
                'title' => "الحلقة $i",
                'description' => "وصف الحلقة $i من مسلسل الاختيار الموسم الأول",
                'season_id' => 1, // الموسم الأول من مسلسل الاختيار
                'episode_number' => $i,
                'duration' => rand(40, 50), // مدة الحلقة بالدقائق
                'video_url' => "https://example.com/series/elikhteyar/s01/e$i.mp4",
                'thumbnail' => "series/elikhteyar/s01/e$i.jpg",
                'views' => rand(10000, 50000),
                'release_date' => Carbon::create(2020, 4, 24)->addDays($i-1),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        // مسلسل الاختيار - الموسم الثاني (10 حلقات)
        for ($i = 1; $i <= 10; $i++) {
            $episodes[] = [
                'title' => "الحلقة $i",
                'description' => "وصف الحلقة $i من مسلسل الاختيار الموسم الثاني",
                'season_id' => 2, // الموسم الثاني من مسلسل الاختيار
                'episode_number' => $i,
                'duration' => rand(40, 50), // مدة الحلقة بالدقائق
                'video_url' => "https://example.com/series/elikhteyar/s02/e$i.mp4",
                'thumbnail' => "series/elikhteyar/s02/e$i.jpg",
                'views' => rand(10000, 50000),
                'release_date' => Carbon::create(2021, 4, 13)->addDays($i-1),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        // مسلسل البرنس (15 حلقة)
        for ($i = 1; $i <= 15; $i++) {
            $episodes[] = [
                'title' => "الحلقة $i",
                'description' => "وصف الحلقة $i من مسلسل البرنس",
                'season_id' => 3, // الموسم الأول من مسلسل البرنس
                'episode_number' => $i,
                'duration' => rand(40, 50), // مدة الحلقة بالدقائق
                'video_url' => "https://example.com/series/elprince/s01/e$i.mp4",
                'thumbnail' => "series/elprince/s01/e$i.jpg",
                'views' => rand(8000, 40000),
                'release_date' => Carbon::create(2020, 4, 24)->addDays($i-1),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        // مسلسل لعبة نيوتن (15 حلقة)
        for ($i = 1; $i <= 15; $i++) {
            $episodes[] = [
                'title' => "الحلقة $i",
                'description' => "وصف الحلقة $i من مسلسل لعبة نيوتن",
                'season_id' => 4, // الموسم الأول من مسلسل لعبة نيوتن
                'episode_number' => $i,
                'duration' => rand(40, 50), // مدة الحلقة بالدقائق
                'video_url' => "https://example.com/series/newton/s01/e$i.mp4",
                'thumbnail' => "series/newton/s01/e$i.jpg",
                'views' => rand(5000, 30000),
                'release_date' => Carbon::create(2021, 4, 13)->addDays($i-1),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        // مسلسل الكبير أوي - الموسم السادس (15 حلقة)
        for ($i = 1; $i <= 15; $i++) {
            $episodes[] = [
                'title' => "الحلقة $i",
                'description' => "وصف الحلقة $i من مسلسل الكبير أوي الموسم السادس",
                'season_id' => 5, // الموسم السادس من مسلسل الكبير أوي
                'episode_number' => $i,
                'duration' => rand(25, 35), // مدة الحلقة بالدقائق
                'video_url' => "https://example.com/series/elkabeer/s06/e$i.mp4",
                'thumbnail' => "series/elkabeer/s06/e$i.jpg",
                'views' => rand(15000, 60000),
                'release_date' => Carbon::create(2022, 4, 3)->addDays($i-1),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        // إدخال البيانات إلى قاعدة البيانات
        DB::table('episodes')->insert($episodes);

        $this->command->info('تم إضافة بيانات حلقات المسلسلات بنجاح!');
    }
}