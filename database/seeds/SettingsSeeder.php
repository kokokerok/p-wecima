<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettingsSeeder extends Seeder
{
    /**
     * تشغيل عملية إضافة البيانات الافتراضية للإعدادات
     *
     * @return void
     */
    public function run()
    {
        // مسح البيانات السابقة (اختياري)
        // DB::table('settings')->truncate();

        // إضافة بيانات الإعدادات
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'WeCima',
                'group' => 'general',
                'type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'site_description',
                'value' => 'موقع WeCima لمشاهدة الأفلام والمسلسلات العربية والأجنبية',
                'group' => 'general',
                'type' => 'textarea',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'site_logo',
                'value' => 'uploads/logo.png',
                'group' => 'general',
                'type' => 'file',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'site_favicon',
                'value' => 'uploads/favicon.ico',
                'group' => 'general',
                'type' => 'file',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'site_email',
                'value' => 'info@wecima.com',
                'group' => 'contact',
                'type' => 'email',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'site_phone',
                'value' => '+20123456789',
                'group' => 'contact',
                'type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'site_address',
                'value' => 'القاهرة، مصر',
                'group' => 'contact',
                'type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'facebook_url',
                'value' => 'https://facebook.com/wecima',
                'group' => 'social',
                'type' => 'url',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'twitter_url',
                'value' => 'https://twitter.com/wecima',
                'group' => 'social',
                'type' => 'url',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'instagram_url',
                'value' => 'https://instagram.com/wecima',
                'group' => 'social',
                'type' => 'url',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'youtube_url',
                'value' => 'https://youtube.com/wecima',
                'group' => 'social',
                'type' => 'url',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'primary_color',
                'value' => '#e50914',
                'group' => 'appearance',
                'type' => 'color',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'secondary_color',
                'value' => '#221f1f',
                'group' => 'appearance',
                'type' => 'color',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'dark_color',
                'value' => '#141414',
                'group' => 'appearance',
                'type' => 'color',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'light_color',
                'value' => '#f4f4f4',
                'group' => 'appearance',
                'type' => 'color',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'success_color',
                'value' => '#28a745',
                'group' => 'appearance',
                'type' => 'color',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'warning_color',
                'value' => '#ffc107',
                'group' => 'appearance',
                'type' => 'color',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'danger_color',
                'value' => '#dc3545',
                'group' => 'appearance',
                'type' => 'color',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'main_font',
                'value' => 'Cairo',
                'group' => 'appearance',
                'type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'movies_per_page',
                'value' => '20',
                'group' => 'content',
                'type' => 'number',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'series_per_page',
                'value' => '20',
                'group' => 'content',
                'type' => 'number',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'featured_movies_count',
                'value' => '10',
                'group' => 'content',
                'type' => 'number',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'featured_series_count',
                'value' => '10',
                'group' => 'content',
                'type' => 'number',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'allow_comments',
                'value' => '1',
                'group' => 'comments',
                'type' => 'boolean',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'moderate_comments',
                'value' => '1',
                'group' => 'comments',
                'type' => 'boolean',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'allow_ratings',
                'value' => '1',
                'group' => 'ratings',
                'type' => 'boolean',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'allow_user_registration',
                'value' => '1',
                'group' => 'users',
                'type' => 'boolean',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'email_verification',
                'value' => '1',
                'group' => 'users',
                'type' => 'boolean',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'google_analytics_id',
                'value' => 'UA-XXXXXXXXX-X',
                'group' => 'analytics',
                'type' => 'text',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'group' => 'system',
                'type' => 'boolean',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'الموقع قيد الصيانة حاليًا، يرجى المحاولة لاحقًا.',
                'group' => 'system',
                'type' => 'textarea',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // إدخال البيانات إلى قاعدة البيانات
        DB::table('settings')->insert($settings);

        $this->command->info('تم إضافة بيانات الإعدادات بنجاح!');
    }
}