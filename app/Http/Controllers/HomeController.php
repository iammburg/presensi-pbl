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
        if (auth()->user()->hasRole('Admin Sekolah')) {

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
        } else {
            return view('home');
        }
    }
}
