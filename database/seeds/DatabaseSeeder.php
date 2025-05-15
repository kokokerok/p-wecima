<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * تشغيل عملية إضافة البيانات الافتراضية
     *
     * @return void
     */
    public function run()
    {
        // ترتيب استدعاء ملفات البذور مهم لضمان تسلسل البيانات بشكل صحيح
        $this->call(UsersSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(GenresSeeder::class);
        $this->call(ActorsSeeder::class);
        $this->call(DirectorsSeeder::class);
        $this->call(MoviesSeeder::class);
        $this->call(SeriesSeeder::class);
        $this->call(SeasonsSeeder::class);
        $this->call(EpisodesSeeder::class);
        $this->call(CommentsSeeder::class);
        $this->call(RatingsSeeder::class);
        $this->call(SettingsSeeder::class);
    }
}