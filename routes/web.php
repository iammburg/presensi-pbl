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

Route::get('/', function () {
    return view('welcome');
});

Route::permanentRedirect('/', '/login');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::resource('profil', ProfilController::class)->except('destroy');

// Route buat Admin Sistem
Route::resource('manage-user', UserController::class);
Route::resource('manage-role', RoleController::class);
Route::resource('manage-menu', MenuController::class);
Route::resource('manage-permission', PermissionController::class)->only('store', 'destroy');

// Route buat Admin Sekolah
Route::resource('manage-academic-years', AcademicYearController::class);
Route::resource('manage-classes', SchoolClassController::class);
Route::resource('manage-subjects', SubjectController::class);
Route::resource('manage-teachers', TeacherController::class);
Route::post('manage-teachers/import', [TeacherController::class, 'import'] )->name('manage-teachers.import');
Route::get('manage-teachers/template/download', [TeacherController::class, 'downloadTemplate'])->name('manage-teachers.template');
Route::resource('manage-students', StudentController::class);
Route::post('manage-students/import', [StudentController::class, 'import'])->name('manage-students.import');
Route::get('manage-students/template/download', [StudentController::class, 'downloadTemplate'])->name('manage-students.template');

Route::get('dbbackup', [DBBackupController::class, 'DBDataBackup']);