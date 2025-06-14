<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Psy\Util\Json;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Student;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    public function index()
    {
        if (Auth::user()->hasRole('Admin Sekolah')) {

            // Total siswa aktif
            $activeStudents = Student::where('is_active', true)->count();

            // Total Hadir
            $present = Attendance::where('status', 'Hadir')->count();

            // Total Alpha
            $absent = Attendance::where('status', 'Alpha')->count();

            // Total Izin
            $excused = Attendance::where('status', 'Izin')->count();

            // Chart: jumlah hadir per kelas
            $chart = Attendance::where('status', 'Hadir')
                ->with('classSchedule.schoolClass') // load hingga ke kelas
                ->get()
                ->groupBy(function ($attendance) {
                    return optional($attendance->classSchedule->schoolClass)->name;
                })
                ->map(function ($attendances, $className) {
                    return [
                        'class' => $className ?? 'Tidak diketahui',
                        'total_present' => $attendances->count()
                    ];
                })
                ->values();



            return view('home', compact('activeStudents', 'present', 'absent', 'excused', 'chart'));
        } else if (Auth::user()->hasRole('Guru BK')) {
            // Top 3 siswa dengan poin prestasi tertinggi
            $topAchievementStudents = \App\Models\Student::select('students.nisn', 'students.name')
                ->leftJoin('student_class_assignments as sca', 'sca.student_id', '=', 'students.nisn')
                ->leftJoin('classes as sc', 'sca.class_id', '=', 'sc.id')
                ->leftJoin('achievements', function($join) {
                    $join->on('achievements.student_id', '=', 'students.nisn')
                        ->where('achievements.validation_status', 'approved');
                })
                ->leftJoin('achievement_points as ap', 'achievements.achievement_points_id', '=', 'ap.id')
                ->groupBy('students.nisn', 'students.name', 'sc.name', 'sc.parallel_name')
                ->selectRaw('COALESCE(SUM(ap.points),0) as total_point, sc.name as class_name, sc.parallel_name')
                ->orderByDesc('total_point')
                ->limit(3)
                ->get()
                ->map(function($item) {
                    $kelas = $item->class_name ? ($item->class_name . ($item->parallel_name ? ' - ' . $item->parallel_name : '')) : '-';
                    return (object)[
                        'name' => $item->name,
                        'class_name' => $kelas,
                        'total_point' => $item->total_point
                    ];
                });

            // Top 3 siswa dengan poin pelanggaran tertinggi
            $topViolationStudents = \App\Models\Student::select('students.nisn', 'students.name')
                ->leftJoin('student_class_assignments as sca', 'sca.student_id', '=', 'students.nisn')
                ->leftJoin('classes as sc', 'sca.class_id', '=', 'sc.id')
                ->leftJoin('violations', function($join) {
                    $join->on('violations.student_id', '=', 'students.nisn')
                        ->where('violations.validation_status', 'approved');
                })
                ->leftJoin('violation_points as vp', 'violations.violation_points_id', '=', 'vp.id')
                ->groupBy('students.nisn', 'students.name', 'sc.name', 'sc.parallel_name')
                ->selectRaw('COALESCE(SUM(vp.points),0) as total_point, sc.name as class_name, sc.parallel_name')
                ->orderByDesc('total_point')
                ->limit(3)
                ->get()
                ->map(function($item) {
                    $kelas = $item->class_name ? ($item->class_name . ($item->parallel_name ? ' - ' . $item->parallel_name : '')) : '-';
                    return (object)[
                        'name' => $item->name,
                        'class_name' => $kelas,
                        'total_point' => $item->total_point
                    ];
                });

            return view('home', compact('topAchievementStudents', 'topViolationStudents'));
        } else {
            return view('home');
        }
    }
}
