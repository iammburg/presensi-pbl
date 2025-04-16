<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolAdminMenu = Menu::create([
            'nama_menu' => 'Menu Manajemen',
            'url' => '#',
            'icon' => '',
            'parent_id' => '0',
            'urutan' => 1
        ]);

        Menu::create([
            'nama_menu' => 'Dashboard',
            'url' => 'home',
            'icon' => 'fas fa-home',
            'parent_id' => $schoolAdminMenu->id,
            'urutan' => 1
        ]);

        $schoolAdminSubMenu = Menu::create([
            'nama_menu' => 'Kelola Data Sekolah',
            'url' => '#',
            'icon' => 'fas fa-school',
            'parent_id' => $schoolAdminMenu->id,
            'urutan' => 2
        ]);

        $subMenuId = Menu::create([
            'nama_menu' => 'Manajemen Tahun Akademik',
            'url' => 'manage-academic-years',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 1
        ]);

        Permission::create(['name' => 'create_academic_year', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_academic_year', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_academic_year', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_academic_year', 'menu_id' => $subMenuId->id]);

        $subMenuId = Menu::create([
            'nama_menu' => 'Manajemen Data Kelas',
            'url' => 'manage-classes',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 2
        ]);

        Permission::create(['name' => 'create_class', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_class', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_class', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_class', 'menu_id' => $subMenuId->id]);

        $subMenuId = Menu::create([
            'nama_menu' => 'Manajemen Data Mapel',
            'url' => 'manage-subjects',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 3
        ]);

        Permission::create(['name' => 'create_subject', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_subject', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_subject', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_subject', 'menu_id' => $subMenuId->id]);

        $subMenuId = Menu::create([
            'nama_menu' => 'Manajemen Data Guru',
            'url' => 'manage-teachers',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 4
        ]);

        Permission::create(['name' => 'create_teacher', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_teacher', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_teacher', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_teacher', 'menu_id' => $subMenuId->id]);

        $subMenuId = Menu::create([
            'nama_menu' => 'Manajemen Data Siswa',
            'url' => 'manage-students',
            'parent_id' => $schoolAdminSubMenu->id,
            'urutan' => 5
        ]);

        Permission::create(['name' => 'create_student', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'read_student', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'update_student', 'menu_id' => $subMenuId->id]);
        Permission::create(['name' => 'delete_student', 'menu_id' => $subMenuId->id]);

        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [9, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [10, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [11, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [12, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [13, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [14, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [15, 2]);
        DB::insert('insert into role_has_menus (menu_id, role_id) values (?, ?)', [16, 2]);

        User::factory()->create([
            'name' => 'Admin Sekolah 1',
            'email' => 'adminsekolah1@gmail.com',
            'password' => Hash::make("4dm1nS3k0l4h_879867yhdaad89u"),
        ]);

        $schoolAdmin = Role::create(['name' => 'Admin Sekolah']);
        $schoolAdmin->givePermissionTo([
            'create_academic_year',
            'read_academic_year',
            'update_academic_year',
            'delete_academic_year',
            'create_class',
            'read_class',
            'update_class',
            'delete_class',
            'create_subject',
            'read_subject',
            'update_subject',
            'delete_subject',
            'create_teacher',
            'read_teacher',
            'update_teacher',
            'delete_teacher',
            'create_student',
            'read_student',
            'update_student',
            'delete_student'
        ]);
        User::firstWhere('email', 'adminsekolah1@gmail.com')->assignRole('Admin Sekolah');
    }
}
