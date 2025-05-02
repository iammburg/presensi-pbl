<?php

use App\Http\Controllers\PrestasiController;
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
use App\Http\Controllers\ViolationPointController;
use App\Http\Controllers\HomeroomAssignmentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::prefix('achievement-points')->group(function () {
    Route::get('/kelola-prestasi', [PrestasiController::class, 'index'])->name('prestasi.kelola');
    Route::get('/riwayat', [PrestasiController::class, 'riwayat'])->name('prestasi.riwayat');
    Route::get('/create', [PrestasiController::class, 'create'])->name('prestasi.create');
    Route::post('/simpan', [PrestasiController::class, 'store'])->name('prestasi.simpan');
    Route::get('/edit/{id}', [PrestasiController::class, 'edit'])->name('prestasi.edit');
    Route::put('/update/{id}', [PrestasiController::class, 'update'])->name('prestasi.update');
    Route::delete('/hapus/{id}', [PrestasiController::class, 'destroy'])->name('prestasi.hapus');
    Route::get('/prestasi/laporan', [PrestasiController::class, 'laporan'])->name('prestasi.laporan');
    Route::get('/prestasi/update-status/{id}/{status}', [PrestasiController::class, 'updateStatus'])->name('prestasi.updateStatus');
});

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
Route::resource('kelola-prestasi', PrestasiController::class);
Route::post('manage-students/import', [StudentController::class, 'import'])->name('manage-students.import');
Route::get('manage-students/template/download', [StudentController::class, 'downloadTemplate'])->name('manage-students.template');
Route::resource('manage-homeroom-assignments', HomeroomAssignmentController::class);
Route::resource('kelola-pelanggaran', ViolationPointController::class);

Route::get('dbbackup', [DBBackupController::class, 'DBDataBackup']);
