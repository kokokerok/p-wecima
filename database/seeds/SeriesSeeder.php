<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeriesSeeder extends Seeder
{
    /**
     * تشغيل عملية إضافة البيانات الافتراضية للمسلسلات
     *
     * @return void
     */
    public function run()
    {
        // إضافة بيانات المسلسلات
        $series = [
            [
                'title' => 'مسلسل الاختيار',
                'description' => 'مسلسل درامي مصري يتناول قصص حقيقية لشهداء الجيش والشرطة المصرية في مواجهة الإرهاب.',
                'year' => 2020,
                'rating' => 8.5,
                'category_id' => 2, // تصنيف دراما
                'seasons_count' => 3,
                'episodes_count' => 30,
                'poster' => 'series/elikhteyar.jpg',
                'banner' => 'series/banners/elikhteyar.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example1',
                'status' => 'active',
                'featured' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'مسلسل البرنس',
                'description' => 'مسلسل درامي مصري يتناول قصة رجل من منطقة شعبية يدخل في صراعات مع عائلته.',
                'year' => 2020,
                'rating' => 7.8,
                'category_id' => 2, // تصنيف دراما
                'seasons_count' => 1,
                'episodes_count' => 30,
                'poster' => 'series/elprince.jpg',
                'banner' => 'series/banners/elprince.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example2',
                'status' => 'active',
                'featured' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'مسلسل لعبة نيوتن',
                'description' => 'مسلسل درامي مصري يتناول قصة مهندس ميكانيكي يعمل في مجال صناعة السيارات.',
                'year' => 2021,
                'rating' => 7.5,
                'category_id' => 6, // تصنيف غموض
                'seasons_count' => 1,
                'episodes_count' => 15,
                'poster' => 'series/newton.jpg',
                'banner' => 'series/banners/newton.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example3',
                'status' => 'active',
                'featured' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'مسلسل النمر',
                'description' => 'مسلسل درامي مصري يتناول قصة شاب يعمل في التجارة ويدخل في صراعات مع عائلات تجارية كبيرة.',
                'year' => 2021,
                'rating' => 7.2,
                'category_id' => 2, // تصنيف دراما
                'seasons_count' => 1,
                'episodes_count' => 30,
                'poster' => 'series/elnemer.jpg',
                'banner' => 'series/banners/elnemer.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example4',
                'status' => 'active',
                'featured' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'مسلسل ضد الكسر',
                'description' => 'مسلسل درامي مصري يتناول قصة طبيبة نفسية تواجه تحديات في حياتها المهنية والشخصية.',
                'year' => 2022,
                'rating' => 8.0,
                'category_id' => 2, // تصنيف دراما
                'seasons_count' => 1,
                'episodes_count' => 15,
                'poster' => 'series/ded_elkasr.jpg',
                'banner' => 'series/banners/ded_elkasr.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example5',
                'status' => 'active',
                'featured' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'مسلسل الكبير أوي',
                'description' => 'مسلسل كوميدي مصري يتناول قصة عمدة إحدى القرى ومواقفه الكوميدية مع أهل القرية.',
                'year' => 2022,
                'rating' => 8.3,
                'category_id' => 3, // تصنيف كوميدي
                'seasons_count' => 6,
                'episodes_count' => 90,
                'poster' => 'series/elkabeer.jpg',
                'banner' => 'series/banners/elkabeer.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example6',
                'status' => 'active',
                'featured' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'مسلسل الوسم',
                'description' => 'مسلسل درامي سعودي يتناول قصة جريمة غامضة وتحقيقات الشرطة للكشف عن الجاني.',
                'year' => 2022,
                'rating' => 7.9,
                'category_id' => 6, // تصنيف غموض
                'seasons_count' => 1,
                'episodes_count' => 8,
                'poster' => 'series/alwasm.jpg',
                'banner' => 'series/banners/alwasm.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example7',
                'status' => 'active',
                'featured' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'مسلسل رشاش',
                'description' => 'مسلسل درامي سعودي يتناول قصة حقيقية لأحد أشهر المجرمين في المملكة العربية السعودية.',
                'year' => 2021,
                'rating' => 8.2,
                'category_id' => 6, // تصنيف غموض
                'seasons_count' => 1,
                'episodes_count' => 15,
                'poster' => 'series/rashash.jpg',
                'banner' => 'series/banners/rashash.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example8',
                'status' => 'active',
                'featured' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // إدخال البيانات إلى قاعدة البيانات
        DB::table('series')->insert($series);

        // إضافة العلاقات بين المسلسلات والممثلين والمخرجين والأنواع
        $series_actor = [];
        $series_director = [];
        $series_genre = [];

        for ($i = 1; $i <= 8; $i++) {
            // إضافة 3-5 ممثلين لكل مسلسل
            $actor_count = rand(3, 5);
            for ($j = 1; $j <= $actor_count; $j++) {
                $actor_id = rand(1, 20); // افتراض وجود 20 ممثل
                $series_actor[] = [
                    'series_id' => $i,
                    'actor_id' => $actor_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            // إضافة مخرج واحد أو اثنين لكل مسلسل
            $director_count = rand(1, 2);
            for ($j = 1; $j <= $director_count; $j++) {
                $director_id = rand(1, 10); // افتراض وجود 10 مخرجين
                $series_director[] = [
                    'series_id' => $i,
                    'director_id' => $director_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            // إضافة 1-3 أنواع لكل مسلسل
            $genre_count = rand(1, 3);
            for ($j = 1; $j <= $genre_count; $j++) {
                $genre_id = rand(1, 8); // افتراض وجود 8 أنواع
                $series_genre[] = [
                    'series_id' => $i,
                    'genre_id' => $genre_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        // إدخال العلاقات إلى قاعدة البيانات
        DB::table('series_actor')->insert($series_actor);
        DB::table('series_director')->insert($series_director);
        DB::table('series_genre')->insert($series_genre);

        $this->command->info('تم إضافة بيانات المسلسلات بنجاح!');
    }
}