<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MoviesSeeder extends Seeder
{
    /**
     * تشغيل عملية إضافة بيانات الأفلام الافتراضية
     *
     * @return void
     */
    public function run()
    {
        // إضافة بيانات الأفلام
        $movies = [
            [
                'title' => 'كيرة والجن',
                'description' => 'فيلم مصري يتناول فترة الاحتلال الإنجليزي لمصر وثورة 1919 من خلال قصة حب بين شاب وفتاة.',
                'year' => 2022,
                'duration' => 150,
                'rating' => 7.8,
                'category_id' => 1, // تصنيف أكشن
                'poster' => 'movies/kira_wal_jin.jpg',
                'banner' => 'movies/banners/kira_wal_jin.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example1',
                'status' => 'active',
                'featured' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'البعض لا يذهب للمأذون مرتين',
                'description' => 'فيلم كوميدي مصري يتناول قصة رجل يتزوج من امرأتين في نفس الوقت ويحاول إخفاء الأمر عنهما.',
                'year' => 2021,
                'duration' => 120,
                'rating' => 7.2,
                'category_id' => 3, // تصنيف كوميدي
                'poster' => 'movies/elbaad_la_yazhab.jpg',
                'banner' => 'movies/banners/elbaad_la_yazhab.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example2',
                'status' => 'active',
                'featured' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'العارف',
                'description' => 'فيلم أكشن مصري يتناول قصة هاكر مصري يتورط في قضية دولية.',
                'year' => 2021,
                'duration' => 135,
                'rating' => 7.5,
                'category_id' => 1, // تصنيف أكشن
                'poster' => 'movies/elaref.jpg',
                'banner' => 'movies/banners/elaref.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example3',
                'status' => 'active',
                'featured' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'ماكو',
                'description' => 'فيلم رعب مصري يتناول قصة مجموعة من الشباب يواجهون أحداثاً غامضة في رحلة بحرية.',
                'year' => 2022,
                'duration' => 110,
                'rating' => 6.8,
                'category_id' => 4, // تصنيف رعب
                'poster' => 'movies/mako.jpg',
                'banner' => 'movies/banners/mako.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example4',
                'status' => 'active',
                'featured' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'موسى',
                'description' => 'فيلم خيال علمي مصري يتناول قصة طالب هندسة يبتكر روبوتاً ذكياً يدعى موسى.',
                'year' => 2021,
                'duration' => 140,
                'rating' => 7.9,
                'category_id' => 5, // تصنيف خيال علمي
                'poster' => 'movies/musa.jpg',
                'banner' => 'movies/banners/musa.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example5',
                'status' => 'active',
                'featured' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'صندوق الدنيا',
                'description' => 'فيلم درامي مصري يتناول ثلاث قصص مختلفة تدور في إطار اجتماعي.',
                'year' => 2020,
                'duration' => 125,
                'rating' => 7.3,
                'category_id' => 2, // تصنيف دراما
                'poster' => 'movies/sandoq_eldonia.jpg',
                'banner' => 'movies/banners/sandoq_eldonia.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example6',
                'status' => 'active',
                'featured' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => '200 جنيه',
                'description' => 'فيلم درامي مصري يتناول قصص متعددة مرتبطة بورقة نقدية من فئة 200 جنيه.',
                'year' => 2021,
                'duration' => 130,
                'rating' => 7.4,
                'category_id' => 2, // تصنيف دراما
                'poster' => 'movies/200_pounds.jpg',
                'banner' => 'movies/banners/200_pounds.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example7',
                'status' => 'active',
                'featured' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'أحمد نوتردام',
                'description' => 'فيلم كوميدي مصري يتناول قصة شاب يعاني من تشوه خلقي ويواجه صعوبات في المجتمع.',
                'year' => 2021,
                'duration' => 115,
                'rating' => 6.9,
                'category_id' => 3, // تصنيف كوميدي
                'poster' => 'movies/ahmed_notredame.jpg',
                'banner' => 'movies/banners/ahmed_notredame.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example8',
                'status' => 'active',
                'featured' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'ديدو',
                'description' => 'فيلم كوميدي مصري يتناول قصة شاب يعمل كمدرب كلاب ويتورط في مشاكل مع عصابة.',
                'year' => 2021,
                'duration' => 105,
                'rating' => 6.7,
                'category_id' => 3, // تصنيف كوميدي
                'poster' => 'movies/dido.jpg',
                'banner' => 'movies/banners/dido.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example9',
                'status' => 'active',
                'featured' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'الفيل الأزرق 2',
                'description' => 'فيلم غموض وإثارة مصري يتناول قصة طبيب نفسي يحاول حل لغز جريمة غامضة.',
                'year' => 2019,
                'duration' => 130,
                'rating' => 8.2,
                'category_id' => 6, // تصنيف غموض
                'poster' => 'movies/blue_elephant2.jpg',
                'banner' => 'movies/banners/blue_elephant2.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example10',
                'status' => 'active',
                'featured' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // إدخال البيانات إلى قاعدة البيانات
        DB::table('movies')->insert($movies);

        // إضافة العلاقات بين الأفلام والممثلين والمخرجين والأنواع
        $movie_actor = [];
        $movie_director = [];
        $movie_genre = [];

        for ($i = 1; $i <= 10; $i++) {
            // إضافة 3-5 ممثلين لكل فيلم
            $actor_count = rand(3, 5);
            for ($j = 1; $j <= $actor_count; $j++) {
                $actor_id = rand(1, 20); // افتراض وجود 20 ممثل
                $movie_actor[] = [
                    'movie_id' => $i,
                    'actor_id' => $actor_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            // إضافة مخرج واحد أو اثنين لكل فيلم
            $director_count = rand(1, 2);
            for ($j = 1; $j <= $director_count; $j++) {
                $director_id = rand(1, 10); // افتراض وجود 10 مخرجين
                $movie_director[] = [
                    'movie_id' => $i,
                    'director_id' => $director_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            // إضافة 1-3 أنواع لكل فيلم
            $genre_count = rand(1, 3);
            for ($j = 1; $j <= $genre_count; $j++) {
                $genre_id = rand(1, 8); // افتراض وجود 8 أنواع
                $movie_genre[] = [
                    'movie_id' => $i,
                    'genre_id' => $genre_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        // إدخال العلاقات إلى قاعدة البيانات
        DB::table('movie_actor')->insert($movie_actor);
        DB::table('movie_director')->insert($movie_director);
        DB::table('movie_genre')->insert($movie_genre);

        $this->command->info('تم إضافة بيانات الأفلام بنجاح!');
    }
}