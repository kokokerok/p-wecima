<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DirectorsSeeder extends Seeder
{
    /**
     * تشغيل عملية إضافة البيانات الافتراضية للمخرجين
     *
     * @return void
     */
    public function run()
    {
        // مسح البيانات السابقة (اختياري)
        // DB::table('directors')->truncate();

        // إضافة بيانات المخرجين
        $directors = [
            [
                'name' => 'بيتر جاكسون',
                'bio' => 'مخرج ومنتج وكاتب سيناريو نيوزيلندي، اشتهر بإخراج سلسلة أفلام "سيد الخواتم" و"الهوبيت".',
                'image' => 'directors/peter_jackson.jpg',
                'nationality' => 'نيوزيلندا',
                'birth_date' => '1961-10-31',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'كريستوفر نولان',
                'bio' => 'مخرج ومنتج وكاتب سيناريو بريطاني-أمريكي، اشتهر بأفلامه مثل "انسيبشن" و"دنكيرك" و"انترستيلر".',
                'image' => 'directors/christopher_nolan.jpg',
                'nationality' => 'بريطانيا',
                'birth_date' => '1970-07-30',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'ستيفن سبيلبرغ',
                'bio' => 'مخرج ومنتج أمريكي، يعتبر أحد أكثر المخرجين نجاحًا في تاريخ السينما، من أشهر أفلامه "جوراسيك بارك" و"إي تي".',
                'image' => 'directors/steven_spielberg.jpg',
                'nationality' => 'الولايات المتحدة',
                'birth_date' => '1946-12-18',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'مارتن سكورسيزي',
                'bio' => 'مخرج ومنتج أمريكي، اشتهر بأفلامه مثل "الرجل الأيرلندي" و"ذئب وول ستريت".',
                'image' => 'directors/martin_scorsese.jpg',
                'nationality' => 'الولايات المتحدة',
                'birth_date' => '1942-11-17',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'كوينتن تارانتينو',
                'bio' => 'مخرج وكاتب سيناريو أمريكي، اشتهر بأفلامه مثل "بالب فيكشن" و"اقتل بيل".',
                'image' => 'directors/quentin_tarantino.jpg',
                'nationality' => 'الولايات المتحدة',
                'birth_date' => '1963-03-27',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'بيتر فاريلي',
                'bio' => 'مخرج وكاتب سيناريو أمريكي، اشتهر بأفلامه الكوميدية مثل "الكتاب الأخضر".',
                'image' => 'directors/peter_farrelly.jpg',
                'nationality' => 'الولايات المتحدة',
                'birth_date' => '1956-12-17',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'مروان حامد',
                'bio' => 'مخرج مصري، اشتهر بأفلامه مثل "الفيل الأزرق" و"تراب الماس".',
                'image' => 'directors/marwan_hamed.jpg',
                'nationality' => 'مصر',
                'birth_date' => '1977-01-01',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'شريف عرفة',
                'bio' => 'مخرج مصري، اشتهر بأفلامه مثل "الجزيرة" و"ولاد رزق".',
                'image' => 'directors/sherif_arafa.jpg',
                'nationality' => 'مصر',
                'birth_date' => '1960-01-01',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'نادين لبكي',
                'bio' => 'مخرجة وممثلة لبنانية، اشتهرت بأفلامها مثل "كفرناحوم" و"هلأ لوين".',
                'image' => 'directors/nadine_labaki.jpg',
                'nationality' => 'لبنان',
                'birth_date' => '1974-02-18',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'هيفاء المنصور',
                'bio' => 'مخرجة سعودية، اشتهرت بفيلمها "وجدة" وهو أول فيلم روائي طويل يتم تصويره بالكامل في المملكة العربية السعودية.',
                'image' => 'directors/haifaa_al_mansour.jpg',
                'nationality' => 'السعودية',
                'birth_date' => '1974-08-10',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // إدخال البيانات إلى قاعدة البيانات
        DB::table('directors')->insert($directors);

        $this->command->info('تم إضافة بيانات المخرجين بنجاح!');
    }
}