<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Vacations
            ['name' => 'view_vacations', 'label' => 'İzinleri Görüntüle', 'module' => 'İzinler'],
            ['name' => 'create_vacations', 'label' => 'İzin Oluştur', 'module' => 'İzinler'],
            ['name' => 'approve_vacations', 'label' => 'İzin Onayla/Reddet', 'module' => 'İzinler'],
            
            // Tasks
            ['name' => 'view_tasks', 'label' => 'Görevleri Görüntüle', 'module' => 'Görevler'],
            ['name' => 'create_tasks', 'label' => 'Görev Oluştur', 'module' => 'Görevler'],
            ['name' => 'edit_tasks', 'label' => 'Görev Düzenle', 'module' => 'Görevler'],
            ['name' => 'delete_tasks', 'label' => 'Görev Sil', 'module' => 'Görevler'],

            // Chat
            ['name' => 'access_chat', 'label' => 'Sohbete Erişim', 'module' => 'Sohbet'],
            
            // Files
            ['name' => 'view_files', 'label' => 'Dosyaları Görüntüle', 'module' => 'Dosyalar'],
            ['name' => 'upload_files', 'label' => 'Dosya Yükle', 'module' => 'Dosyalar'],
            ['name' => 'delete_files', 'label' => 'Dosya Sil', 'module' => 'Dosyalar'],

            // Announcements
            ['name' => 'view_announcements', 'label' => 'Duyuruları Görüntüle', 'module' => 'Duyurular'],
            ['name' => 'create_announcements', 'label' => 'Duyuru Oluştur', 'module' => 'Duyurular'],
            ['name' => 'edit_announcements', 'label' => 'Duyuru Düzenle', 'module' => 'Duyurular'],
            ['name' => 'delete_announcements', 'label' => 'Duyuru Sil', 'module' => 'Duyurular'],
            
            // Polls
            ['name' => 'view_polls', 'label' => 'Anketleri Görüntüle', 'module' => 'Anketler'],
            ['name' => 'create_polls', 'label' => 'Anket Oluştur', 'module' => 'Anketler'],
            ['name' => 'vote_polls', 'label' => 'Ankete Oy Ver', 'module' => 'Anketler'],

            // Users
            ['name' => 'view_users', 'label' => 'Kullanıcıları Görüntüle', 'module' => 'Kullanıcılar'],
            ['name' => 'create_users', 'label' => 'Kullanıcı Oluştur', 'module' => 'Kullanıcılar'],
            ['name' => 'edit_users', 'label' => 'Kullanıcı Düzenle', 'module' => 'Kullanıcılar'],
            ['name' => 'delete_users', 'label' => 'Kullanıcı Sil', 'module' => 'Kullanıcılar'],
            
            // Roles
            ['name' => 'manage_roles', 'label' => 'Rolleri Yönet', 'module' => 'Ayarlar'],

            // Departments
            ['name' => 'view_departments', 'label' => 'Departmanları Görüntüle', 'module' => 'Departmanlar'],
            ['name' => 'create_departments', 'label' => 'Departman Oluştur', 'module' => 'Departmanlar'],
            ['name' => 'edit_departments', 'label' => 'Departman Düzenle', 'module' => 'Departmanlar'],
            ['name' => 'delete_departments', 'label' => 'Departman Sil', 'module' => 'Departmanlar'],

            // Platform Settings
            ['name' => 'manage_platform_settings', 'label' => 'Platform Ayarlarını Yönet', 'module' => 'Ayarlar'],

            // Meetings
            ['name' => 'view_meetings', 'label' => 'Toplantıları Görüntüle', 'module' => 'Toplantılar'],
            ['name' => 'create_meetings', 'label' => 'Toplantı Oluştur', 'module' => 'Toplantılar'],
            ['name' => 'delete_meetings', 'label' => 'Toplantı Sil', 'module' => 'Toplantılar'],
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
