<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeys extends Migration
{
    /**
     * تنفيذ الترحيل.
     *
     * @return void
     */
    public function up()
    {
        // إضافة المفاتيح الخارجية لجدول المستخدمين
        Schema::table('watch_history', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // إضافة المفاتيح الخارجية لجدول المسلسلات
        Schema::table('seasons', function (Blueprint $table) {
            $table->foreign('series_id')->references('id')->on('series')->onDelete('cascade');
        });

        Schema::table('episodes', function (Blueprint $table) {
            $table->foreign('season_id')->references('id')->on('seasons')->onDelete('cascade');
            $table->foreign('series_id')->references('id')->on('series')->onDelete('cascade');
        });

        // إضافة المفاتيح الخارجية لجدول التعليقات
        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // إضافة المفاتيح الخارجية لجدول التقييمات
        Schema::table('ratings', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // إضافة المفاتيح الخارجية لجداول العلاقات
        Schema::table('movie_actor', function (Blueprint $table) {
            $table->foreign('movie_id')->references('id')->on('movies')->onDelete('cascade');
            $table->foreign('actor_id')->references('id')->on('actors')->onDelete('cascade');
        });

        Schema::table('series_actor', function (Blueprint $table) {
            $table->foreign('series_id')->references('id')->on('series')->onDelete('cascade');
            $table->foreign('actor_id')->references('id')->on('actors')->onDelete('cascade');
        });

        Schema::table('movie_director', function (Blueprint $table) {
            $table->foreign('movie_id')->references('id')->on('movies')->onDelete('cascade');
            $table->foreign('director_id')->references('id')->on('directors')->onDelete('cascade');
        });

        Schema::table('series_director', function (Blueprint $table) {
            $table->foreign('series_id')->references('id')->on('series')->onDelete('cascade');
            $table->foreign('director_id')->references('id')->on('directors')->onDelete('cascade');
        });

        Schema::table('movie_genre', function (Blueprint $table) {
            $table->foreign('movie_id')->references('id')->on('movies')->onDelete('cascade');
            $table->foreign('genre_id')->references('id')->on('genres')->onDelete('cascade');
        });

        Schema::table('series_genre', function (Blueprint $table) {
            $table->foreign('series_id')->references('id')->on('series')->onDelete('cascade');
            $table->foreign('genre_id')->references('id')->on('genres')->onDelete('cascade');
        });

        // إضافة المفاتيح الخارجية لجدول الاشتراكات
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * التراجع عن الترحيل.
     *
     * @return void
     */
    public function down()
    {
        // إزالة المفاتيح الخارجية من جدول المستخدمين
        Schema::table('watch_history', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // إزالة المفاتيح الخارجية من جدول المسلسلات
        Schema::table('seasons', function (Blueprint $table) {
            $table->dropForeign(['series_id']);
        });

        Schema::table('episodes', function (Blueprint $table) {
            $table->dropForeign(['season_id']);
            $table->dropForeign(['series_id']);
        });

        // إزالة المفاتيح الخارجية من جدول التعليقات
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // إزالة المفاتيح الخارجية من جدول التقييمات
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // إزالة المفاتيح الخارجية من جداول العلاقات
        Schema::table('movie_actor', function (Blueprint $table) {
            $table->dropForeign(['movie_id']);
            $table->dropForeign(['actor_id']);
        });

        Schema::table('series_actor', function (Blueprint $table) {
            $table->dropForeign(['series_id']);
            $table->dropForeign(['actor_id']);
        });

        Schema::table('movie_director', function (Blueprint $table) {
            $table->dropForeign(['movie_id']);
            $table->dropForeign(['director_id']);
        });

        Schema::table('series_director', function (Blueprint $table) {
            $table->dropForeign(['series_id']);
            $table->dropForeign(['director_id']);
        });

        Schema::table('movie_genre', function (Blueprint $table) {
            $table->dropForeign(['movie_id']);
            $table->dropForeign(['genre_id']);
        });

        Schema::table('series_genre', function (Blueprint $table) {
            $table->dropForeign(['series_id']);
            $table->dropForeign(['genre_id']);
        });

        // إزالة المفاتيح الخارجية من جدول الاشتراكات
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
}