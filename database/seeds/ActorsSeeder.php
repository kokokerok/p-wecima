<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActorsSeeder extends Seeder
{
    /**
     * تشغيل عملية إضافة البيانات الافتراضية للممثلين
     *
     * @return void
     */
    public function run()
    {
        // مسح البيانات السابقة (اختياري)
        // DB::table('actors')->truncate();

        // إضافة بيانات الممثلين العرب
        $arabActors = [
            [
                'name' => 'أحمد السقا',
                'slug' => 'ahmed-el-sakka',
                'bio' => 'ممثل مصري ولد في 1 مارس 1973، اشتهر بأدواره في أفلام الأكشن والإثارة.',
                'image' => 'actors/ahmed-el-sakka.jpg',
                'gender' => 'male',
                'birth_date' => '1973-03-01',
                'birth_place' => 'مصر',
                'popularity' => 9.5,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'عادل إمام',
                'slug' => 'adel-imam',
                'bio' => 'ممثل مصري ولد في 17 مايو 1940، يعتبر من أهم الممثلين في تاريخ السينما المصرية والعربية.',
                'image' => 'actors/adel-imam.jpg',
                'gender' => 'male',
                'birth_date' => '1940-05-17',
                'birth_place' => 'مصر',
                'popularity' => 9.8,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'منى زكي',
                'slug' => 'mona-zaki',
                'bio' => 'ممثلة مصرية ولدت في 18 نوفمبر 1976، اشتهرت بأدوارها المتنوعة في السينما والتلفزيون.',
                'image' => 'actors/mona-zaki.jpg',
                'gender' => 'female',
                'birth_date' => '1976-11-18',
                'birth_place' => 'مصر',
                'popularity' => 9.0,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'كريم عبد العزيز',
                'slug' => 'karim-abdel-aziz',
                'bio' => 'ممثل مصري ولد في 17 أغسطس 1975، اشتهر بأدواره المتنوعة في السينما المصرية.',
                'image' => 'actors/karim-abdel-aziz.jpg',
                'gender' => 'male',
                'birth_date' => '1975-08-17',
                'birth_place' => 'مصر',
                'popularity' => 9.2,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'نيللي كريم',
                'slug' => 'nelly-karim',
                'bio' => 'ممثلة مصرية ولدت في 18 ديسمبر 1974، اشتهرت بأدوارها الدرامية المميزة.',
                'image' => 'actors/nelly-karim.jpg',
                'gender' => 'female',
                'birth_date' => '1974-12-18',
                'birth_place' => 'مصر',
                'popularity' => 8.9,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'محمد رمضان',
                'slug' => 'mohamed-ramadan',
                'bio' => 'ممثل مصري ولد في 23 مايو 1988، اشتهر بأدوار الأكشن والبطولة المطلقة.',
                'image' => 'actors/mohamed-ramadan.jpg',
                'gender' => 'male',
                'birth_date' => '1988-05-23',
                'birth_place' => 'مصر',
                'popularity' => 8.7,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'ياسمين عبد العزيز',
                'slug' => 'yasmine-abdel-aziz',
                'bio' => 'ممثلة مصرية ولدت في 16 يناير 1980، اشتهرت بأدوارها الكوميدية.',
                'image' => 'actors/yasmine-abdel-aziz.jpg',
                'gender' => 'female',
                'birth_date' => '1980-01-16',
                'birth_place' => 'مصر',
                'popularity' => 8.5,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'عمرو يوسف',
                'slug' => 'amr-youssef',
                'bio' => 'ممثل مصري ولد في 26 مارس 1980، اشتهر بأدواره الدرامية المتنوعة.',
                'image' => 'actors/amr-youssef.jpg',
                'gender' => 'male',
                'birth_date' => '1980-03-26',
                'birth_place' => 'مصر',
                'popularity' => 8.6,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'هند صبري',
                'slug' => 'hend-sabry',
                'bio' => 'ممثلة تونسية ولدت في 20 نوفمبر 1979، اشتهرت بأدوارها في السينما المصرية والعربية.',
                'image' => 'actors/hend-sabry.jpg',
                'gender' => 'female',
                'birth_date' => '1979-11-20',
                'birth_place' => 'تونس',
                'popularity' => 8.8,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'أحمد حلمي',
                'slug' => 'ahmed-helmy',
                'bio' => 'ممثل مصري ولد في 18 نوفمبر 1969، اشتهر بأدواره الكوميدية المميزة.',
                'image' => 'actors/ahmed-helmy.jpg',
                'gender' => 'male',
                'birth_date' => '1969-11-18',
                'birth_place' => 'مصر',
                'popularity' => 9.4,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // إضافة بيانات الممثلين الأجانب
        $foreignActors = [
            [
                'name' => 'توم هانكس',
                'slug' => 'tom-hanks',
                'bio' => 'ممثل أمريكي ولد في 9 يوليو 1956، حائز على جائزة الأوسكار مرتين.',
                'image' => 'actors/tom-hanks.jpg',
                'gender' => 'male',
                'birth_date' => '1956-07-09',
                'birth_place' => 'الولايات المتحدة',
                'popularity' => 9.7,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'ليوناردو دي كابريو',
                'slug' => 'leonardo-dicaprio',
                'bio' => 'ممثل أمريكي ولد في 11 نوفمبر 1974، حائز على جائزة الأوسكار.',
                'image' => 'actors/leonardo-dicaprio.jpg',
                'gender' => 'male',
                'birth_date' => '1974-11-11',
                'birth_place' => 'الولايات المتحدة',
                'popularity' => 9.6,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'سكارليت جوهانسون',
                'slug' => 'scarlett-johansson',
                'bio' => 'ممثلة أمريكية ولدت في 22 نوفمبر 1984، اشتهرت بأدوارها المتنوعة في هوليوود.',
                'image' => 'actors/scarlett-johansson.jpg',
                'gender' => 'female',
                'birth_date' => '1984-11-22',
                'birth_place' => 'الولايات المتحدة',
                'popularity' => 9.3,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'روبرت داوني جونيور',
                'slug' => 'robert-downey-jr',
                'bio' => 'ممثل أمريكي ولد في 4 أبريل 1965، اشتهر بدور آيرون مان في أفلام مارفل.',
                'image' => 'actors/robert-downey-jr.jpg',
                'gender' => 'male',
                'birth_date' => '1965-04-04',
                'birth_place' => 'الولايات المتحدة',
                'popularity' => 9.5,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'جينيفر لورانس',
                'slug' => 'jennifer-lawrence',
                'bio' => 'ممثلة أمريكية ولدت في 15 أغسطس 1990، حائزة على جائزة الأوسكار.',
                'image' => 'actors/jennifer-lawrence.jpg',
                'gender' => 'female',
                'birth_date' => '1990-08-15',
                'birth_place' => 'الولايات المتحدة',
                'popularity' => 9.1,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // دمج جميع الممثلين
        $allActors = array_merge($arabActors, $foreignActors);

        // إدخال البيانات إلى قاعدة البيانات
        DB::table('actors')->insert($allActors);

        $this->command->info('تم إضافة بيانات الممثلين بنجاح!');
    }
}