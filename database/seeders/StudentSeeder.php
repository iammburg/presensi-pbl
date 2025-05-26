<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat parent menu 'Menu Siswa'
        $studentMenu = Menu::create([
            'nama_menu' => 'Menu Siswa',
            'url' => '#',
            'icon' => '',
            'parent_id' => 0,
            'urutan' => 1
        ]);

        // 2. Buat sub-menu 'Dashboard'
        $dashboardMenu = Menu::create([
            'nama_menu' => 'Dashboard',
            'url' => 'home',
            'icon' => 'fas fa-home',
            'parent_id' => $studentMenu->id,
            'urutan' => 1
        ]);
        Permission::create([
            'name' => 'view_dashboard',
            'menu_id' => $dashboardMenu->id
        ]);

        // 3. Buat sub-menu 'Presensi'
        $attendanceMenu = Menu::create([
            'nama_menu' => 'Presensi',
            'url' => 'student-attendance',
            'icon' => 'fas fa-calendar-check',
            'parent_id' => $studentMenu->id,
            'urutan' => 2
        ]);
        Permission::create([
            'name' => 'view_attendance',
            'menu_id' => $attendanceMenu->id
        ]);

        // 4. Buat menu 'Poin' dan sub-menunya
        $pointMenu = Menu::create([
            'nama_menu' => 'Poin',
            'url' => '#',
            'icon' => 'fas fa-star-half-alt',
            'parent_id' => $studentMenu->id,
            'urutan' => 3
        ]);

        $violationMenu = Menu::create([
            'nama_menu' => 'Poin Pelanggaran',
            'url' => 'student-violation-points',
            'parent_id' => $pointMenu->id,
            'urutan' => 1
        ]);
        Permission::create([
            'name' => 'view_violation_points',
            'menu_id' => $violationMenu->id
        ]);

        $achievementMenu = Menu::create([
            'nama_menu' => 'Poin Prestasi',
            'url' => 'student-achievement-points',
            'parent_id' => $pointMenu->id,
            'urutan' => 2
        ]);
        Permission::create([
            'name' => 'view_achievement_points',
            'menu_id' => $achievementMenu->id
        ]);

        // 5. Masukkan semua menu ke role siswa (role_id = 4)
        $menuIds = [
            $studentMenu->id,
            $dashboardMenu->id,
            $attendanceMenu->id,
            $pointMenu->id,
            $violationMenu->id,
            $achievementMenu->id,
        ];

        foreach ($menuIds as $menuId) {
            DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [$menuId, 4]);
        }

        // 6. Buat user siswa contoh
        User::factory()->create([
            'name' => 'Popay',
            'email' => 'siswa1@example.com',
            'password' => Hash::make('Siswa123'),
        ]);

        // 7. Buat dan assign role 'Siswa'
        $studentRole = Role::firstOrCreate(['name' => 'Siswa']);
        $studentRole->givePermissionTo([
            'view_dashboard',
            'view_attendance',
            'view_violation_points',
            'view_achievement_points',
        ]);

        // 8. Assign role ke user siswa
        User::firstWhere('email', 'siswa1@example.com')->assignRole('Siswa');
    }
}
