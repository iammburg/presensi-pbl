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
use App\Http\Controllers\AchievementPointController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\ViolationController;
use App\Http\Controllers\StudentClassAssignmentController;
use App\Http\Controllers\TeachingAssignmentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

// Route buat Admin Sekolah
Route::resource('manage-academic-years', AcademicYearController::class);
Route::resource('manage-classes', SchoolClassController::class);
Route::resource('manage-subjects', SubjectController::class);
Route::resource('manage-teachers', TeacherController::class);
Route::post('manage-teachers/import', [TeacherController::class, 'import'])->name('manage-teachers.import');
Route::get('manage-teachers/template/download', [TeacherController::class, 'downloadTemplate'])->name('manage-teachers.template');
Route::resource('manage-students', StudentController::class);
Route::post('manage-students/import', [StudentController::class, 'import'])->name('manage-students.import');
Route::get('manage-students/template/download', [StudentController::class, 'downloadTemplate'])->name('manage-students.template');
Route::get('/manage-students/{nisn}/detail', [StudentController::class, 'detail']);
Route::resource('manage-homeroom-assignments', HomeroomAssignmentController::class);
Route::resource('manage-teacher-subject-assignments', TeachingAssignmentController::class)
    ->parameters(['manage-teacher-subject-assignments' => 'teacherAssignment']);
Route::resource('manage-student-class-assignments', StudentClassAssignmentController::class)
    ->parameters(['manage-student-class-assignments' => 'studentAssignment']);

// Route buat Guru BK
Route::resource('violation-management', ViolationPointController::class);
Route::resource('achievement-management', AchievementPointController::class);
Route::resource('achievements', AchievementController::class);
Route::resource('violations', ViolationController::class);

Route::get('dbbackup', [DBBackupController::class, 'DBDataBackup']);


// Route tambahan untuk subjects
Route::resource('subjects', SubjectController::class);

// Route tambahan untuk mendapatkan nama jadwal pelajaran
Route::get('subjects/schedule-names', [SubjectController::class, 'getScheduleNames'])->name('subjects.schedule-names');