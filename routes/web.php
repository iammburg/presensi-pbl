<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\DBBackupController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route untuk halaman awal
Route::get('/', function () {
    return view('welcome');
});

// Redirect permanen dari '/' ke '/login'
Route::permanentRedirect('/', '/login');

// Route untuk autentikasi
Auth::routes();

// Route untuk dashboard setelah login
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::resource('subject', SubjectController::class);

// Route untuk profil pengguna
Route::resource('profil', ProfilController::class)->except('destroy');

// Route untuk Admin Sistem
Route::resource('manage-user', UserController::class); // Manajemen pengguna
Route::resource('manage-role', RoleController::class); // Manajemen peran
Route::resource('manage-menu', MenuController::class); // Manajemen menu
Route::resource('manage-permission', PermissionController::class)->only('store', 'destroy'); // Manajemen izin

// Route untuk Admin Sekolah
Route::resource('manage-academic-years', AcademicYearController::class); // Manajemen tahun akademik
Route::resource('manage-classes', SchoolClassController::class); // Manajemen kelas
Route::resource('manage-subjects', SubjectController::class); // Manajemen mata pelajaran
Route::resource('manage-teachers', TeacherController::class); // Manajemen guru
Route::resource('manage-students', StudentController::class); // Manajemen siswa

// Route untuk backup database
Route::get('dbbackup', [DBBackupController::class, 'DBDataBackup']);

// Route tambahan untuk subjects
Route::resource('subjects', SubjectController::class);

// Route tambahan untuk mendapatkan nama jadwal pelajaran
Route::get('subjects/schedule-names', [SubjectController::class, 'getScheduleNames'])->name('subjects.schedule-names');
