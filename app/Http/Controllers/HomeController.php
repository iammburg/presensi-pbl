<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Psy\Util\Json;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

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


    public function index(Request $request)
    {
        // … cek role, hitung $activeStudents, $present, $absent, $excused …
        // Total siswa aktif
        $activeStudents = Student::where('is_active', true)->count();

        // Total Hadir
        $present = Attendance::where('status', 'Hadir')->count();

        // Total Alpha
        $absent = Attendance::where('status', 'Alpha')->count();

        // Total Izin
        $excused = Attendance::where('status', 'Izin')->count();

        // Ambil param week, format: "2025-W24"
        $weekIso = $request->get('week', Carbon::now()->format('o-\WW'));
        // Pecah jadi tahun dan nomor minggu
        list($year, $weekNumber) = explode('-W', $weekIso);

        // Buat Carbon instance di hari Senin minggu itu
        $startOfWeek = Carbon::now()
            ->setISODate((int)$year, (int)$weekNumber)
            ->startOfWeek(); // default Senin
        $endOfWeek   = (clone $startOfWeek)->endOfWeek(); // default Minggu

        // Query attendance khusus minggu terpilih
        $chart = Attendance::where('status', 'Hadir')
            ->whereBetween('meeting_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->with('classSchedule.schoolClass')
            ->get()
            ->groupBy(fn($att) => optional($att->classSchedule->schoolClass)->name)
            
            ->map(fn($atts, $name) => [
                'class'         => $name ?? '–',
                'total_present' => $atts->count(),
            ])->values();

        return view('home', compact(
            'activeStudents',
            'present',
            'absent',
            'excused',
            'chart',
            'weekIso',
            'startOfWeek',
            'endOfWeek'
        ));
    }
}
