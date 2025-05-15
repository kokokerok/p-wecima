<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategoriesSeeder extends Seeder
{
    /**
     * تشغيل عملية إضافة البيانات الافتراضية للتصنيفات
     *
     * @return void
     */
    public function run()
    {
        // مسح البيانات السابقة (اختياري)
        // DB::table('categories')->truncate();

        // إضافة بيانات التصنيفات
        $categories = [
            [
                'name' => 'دراما',
                'slug' => 'drama',
                'description' => 'أفلام ومسلسلات درامية تتناول قصص واقعية وإنسانية',
                'icon' => 'fas fa-theater-masks',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'أكشن',
                'slug' => 'action',
                'description' => 'أفلام ومسلسلات أكشن مليئة بالمغامرات والإثارة',
                'icon' => 'fas fa-fire',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'كوميدي',
                'slug' => 'comedy',
                'description' => 'أفلام ومسلسلات كوميدية للترفيه والضحك',
                'icon' => 'fas fa-laugh',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'رعب',
                'slug' => 'horror',
                'description' => 'أفلام ومسلسلات رعب مخيفة ومثيرة',
                'icon' => 'fas fa-ghost',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'خيال علمي',
                'slug' => 'sci-fi',
                'description' => 'أفلام ومسلسلات خيال علمي تتناول المستقبل والتكنولوجيا',
                'icon' => 'fas fa-rocket',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'رومانسي',
                'slug' => 'romance',
                'description' => 'أفلام ومسلسلات رومانسية تتناول قصص الحب والعلاقات',
                'icon' => 'fas fa-heart',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'مغامرة',
                'slug' => 'adventure',
                'description' => 'أفلام ومسلسلات مغامرات مليئة بالإثارة والتشويق',
                'icon' => 'fas fa-compass',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'غموض',
                'slug' => 'mystery',
                'description' => 'أفلام ومسلسلات غموض وألغاز تتطلب التفكير والتحليل',
                'icon' => 'fas fa-question-circle',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'جريمة',
                'slug' => 'crime',
                'description' => 'أفلام ومسلسلات جريمة تتناول القضايا الجنائية والتحقيقات',
                'icon' => 'fas fa-fingerprint',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'وثائقي',
                'slug' => 'documentary',
                'description' => 'أفلام ومسلسلات وثائقية تعرض قصص واقعية وحقائق',
                'icon' => 'fas fa-film',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // إدخال البيانات إلى قاعدة البيانات
        DB::table('categories')->insert($categories);

        $this->command->info('تم إضافة بيانات التصنيفات بنجاح!');
    }
}