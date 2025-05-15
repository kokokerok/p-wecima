<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenresSeeder extends Seeder
{
    /**
     * تشغيل عملية إضافة البيانات الافتراضية لأنواع الأفلام والمسلسلات
     *
     * @return void
     */
    public function run()
    {
        // مسح البيانات السابقة (اختياري)
        // DB::table('genres')->truncate();

        // إضافة بيانات الأنواع
        $genres = [
            [
                'name' => 'أكشن',
                'description' => 'أفلام ومسلسلات تتضمن مشاهد قتالية ومطاردات ومغامرات مثيرة.',
                'slug' => 'action',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'مغامرة',
                'description' => 'أفلام ومسلسلات تتضمن رحلات استكشافية ومغامرات مثيرة.',
                'slug' => 'adventure',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'كوميديا',
                'description' => 'أفلام ومسلسلات تهدف إلى إضحاك المشاهد من خلال مواقف فكاهية.',
                'slug' => 'comedy',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'جريمة',
                'description' => 'أفلام ومسلسلات تتناول قصص الجرائم والتحقيقات الجنائية.',
                'slug' => 'crime',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'دراما',
                'description' => 'أفلام ومسلسلات تركز على قصص إنسانية وعاطفية وصراعات شخصية.',
                'slug' => 'drama',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'خيال علمي',
                'description' => 'أفلام ومسلسلات تتناول مواضيع علمية وتكنولوجية مستقبلية.',
                'slug' => 'sci-fi',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'رعب',
                'description' => 'أفلام ومسلسلات تهدف إلى إثارة الخوف والرعب لدى المشاهد.',
                'slug' => 'horror',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'رومانسي',
                'description' => 'أفلام ومسلسلات تركز على قصص الحب والعلاقات العاطفية.',
                'slug' => 'romance',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'إثارة وتشويق',
                'description' => 'أفلام ومسلسلات تتضمن عناصر التشويق والإثارة والغموض.',
                'slug' => 'thriller',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'حرب',
                'description' => 'أفلام ومسلسلات تتناول قصص الحروب والصراعات العسكرية.',
                'slug' => 'war',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'غربي',
                'description' => 'أفلام ومسلسلات تدور أحداثها في الغرب الأمريكي القديم.',
                'slug' => 'western',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'تاريخي',
                'description' => 'أفلام ومسلسلات تتناول أحداث تاريخية حقيقية.',
                'slug' => 'historical',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'عائلي',
                'description' => 'أفلام ومسلسلات مناسبة لجميع أفراد العائلة.',
                'slug' => 'family',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'رسوم متحركة',
                'description' => 'أفلام ومسلسلات رسوم متحركة للأطفال والكبار.',
                'slug' => 'animation',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'وثائقي',
                'description' => 'أفلام ومسلسلات وثائقية تتناول قصص وأحداث حقيقية.',
                'slug' => 'documentary',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // إدخال البيانات إلى قاعدة البيانات
        DB::table('genres')->insert($genres);

        $this->command->info('تم إضافة بيانات أنواع الأفلام والمسلسلات بنجاح!');
    }
}