<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersSeeder extends Seeder
{
    /**
     * تشغيل عملية إضافة بيانات المستخدمين الافتراضية
     *
     * @return void
     */
    public function run()
    {
        // إضافة حساب المدير
        DB::table('users')->insert([
            'name' => 'مدير الموقع',
            'email' => 'admin@wecima.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'avatar' => 'users/admin.jpg',
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // إضافة حساب محرر
        DB::table('users')->insert([
            'name' => 'محرر المحتوى',
            'email' => 'editor@wecima.com',
            'password' => Hash::make('editor123'),
            'role' => 'editor',
            'avatar' => 'users/editor.jpg',
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // إضافة مستخدمين عاديين
        $users = [
            [
                'name' => 'أحمد محمد',
                'email' => 'ahmed@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'avatar' => 'users/user1.jpg',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'سارة علي',
                'email' => 'sara@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'avatar' => 'users/user2.jpg',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'محمد خالد',
                'email' => 'mohamed@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'avatar' => 'users/user3.jpg',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'فاطمة أحمد',
                'email' => 'fatma@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'avatar' => 'users/user4.jpg',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'خالد إبراهيم',
                'email' => 'khaled@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'avatar' => 'users/user5.jpg',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('users')->insert($users);

        $this->command->info('تم إضافة بيانات المستخدمين بنجاح!');
    }
}