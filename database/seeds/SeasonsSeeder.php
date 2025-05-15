<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeasonsSeeder extends Seeder
{
    /**
     * تشغيل عملية إضافة البيانات الافتراضية لمواسم المسلسلات
     *
     * @return void
     */
    public function run()
    {
        // مسح البيانات السابقة (اختياري)
        // DB::table('seasons')->truncate();

        // إضافة بيانات المواسم
        $seasons = [
            [
                'series_id' => 1, // مسلسل الاختيار
                'title' => 'الموسم الأول',
                'description' => 'الموسم الأول من مسلسل الاختيار يتناول قصة الشهيد أحمد المنسي قائد الكتيبة 103 صاعقة.',
                'season_number' => 1,
                'year' => 2020,
                'poster' => 'series/elikhteyar/s01/poster.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example1',
                'episodes_count' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'series_id' => 1, // مسلسل الاختيار
                'title' => 'الموسم الثاني',
                'description' => 'الموسم الثاني من مسلسل الاختيار يتناول قصة الشهيد محمد مبروك ضابط الأمن الوطني.',
                'season_number' => 2,
                'year' => 2021,
                'poster' => 'series/elikhteyar/s02/poster.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example2',
                'episodes_count' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'series_id' => 2, // مسلسل البرنس
                'title' => 'الموسم الأول',
                'description' => 'مسلسل البرنس يتناول قصة رجل من منطقة شعبية يدخل في صراعات مع عائلته.',
                'season_number' => 1,
                'year' => 2020,
                'poster' => 'series/elprince/s01/poster.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example3',
                'episodes_count' => 15,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'series_id' => 3, // مسلسل لعبة نيوتن
                'title' => 'الموسم الأول',
                'description' => 'مسلسل لعبة نيوتن يتناول قصة مهندس ميكانيكي يعمل في مجال صناعة السيارات.',
                'season_number' => 1,
                'year' => 2021,
                'poster' => 'series/newton/s01/poster.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example4',
                'episodes_count' => 15,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'series_id' => 6, // مسلسل الكبير أوي
                'title' => 'الموسم السادس',
                'description' => 'الموسم السادس من مسلسل الكبير أوي يتناول مغامرات جديدة للعمدة الكبير أوي.',
                'season_number' => 6,
                'year' => 2022,
                'poster' => 'series/elkabeer/s06/poster.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example5',
                'episodes_count' => 15,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'series_id' => 6, // مسلسل الكبير أوي
                'title' => 'الموسم الخامس',
                'description' => 'الموسم الخامس من مسلسل الكبير أوي يتناول مغامرات العمدة الكبير أوي.',
                'season_number' => 5,
                'year' => 2021,
                'poster' => 'series/elkabeer/s05/poster.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example6',
                'episodes_count' => 15,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'series_id' => 7, // مسلسل الوسم
                'title' => 'الموسم الأول',
                'description' => 'مسلسل الوسم يتناول قصة جريمة غامضة وتحقيقات الشرطة للكشف عن الجاني.',
                'season_number' => 1,
                'year' => 2022,
                'poster' => 'series/alwasm/s01/poster.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example7',
                'episodes_count' => 8,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'series_id' => 8, // مسلسل رشاش
                'title' => 'الموسم الأول',
                'description' => 'مسلسل رشاش يتناول قصة حقيقية لأحد أشهر المجرمين في المملكة العربية السعودية.',
                'season_number' => 1,
                'year' => 2021,
                'poster' => 'series/rashash/s01/poster.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example8',
                'episodes_count' => 15,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // إدخال البيانات إلى قاعدة البيانات
        DB::table('seasons')->insert($seasons);

        $this->command->info('تم إضافة بيانات مواسم المسلسلات بنجاح!');
    }
}