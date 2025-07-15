<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Psy\Util\Json;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        if (Auth::user()->hasRole('superadmin')) {
            return $this->superadminDashboard();
        }

        if (Auth::user()->hasRole('Admin Sekolah')) {
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
        } else if (Auth::user()->hasRole('Guru BK')) {
            // Top 3 siswa dengan poin prestasi tertinggi
            $topAchievementStudents = \App\Models\Student::select('students.nisn', 'students.name')
                ->leftJoin('student_class_assignments as sca', 'sca.student_id', '=', 'students.nisn')
                ->leftJoin('classes as sc', 'sca.class_id', '=', 'sc.id')
                ->leftJoin('achievements', function ($join) {
                    $join->on('achievements.student_id', '=', 'students.nisn')
                        ->where('achievements.validation_status', 'approved');
                })
                ->leftJoin('achievement_points as ap', 'achievements.achievement_points_id', '=', 'ap.id')
                ->groupBy('students.nisn', 'students.name', 'sc.name', 'sc.parallel_name')
                ->selectRaw('COALESCE(SUM(ap.points),0) as total_point, sc.name as class_name, sc.parallel_name')
                ->orderByDesc('total_point')
                ->limit(3)
                ->get()
                ->map(function ($item) {
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
                ->leftJoin('violations', function ($join) {
                    $join->on('violations.student_id', '=', 'students.nisn')
                        ->where('violations.validation_status', 'approved');
                })
                ->leftJoin('violation_points as vp', 'violations.violation_points_id', '=', 'vp.id')
                ->groupBy('students.nisn', 'students.name', 'sc.name', 'sc.parallel_name')
                ->selectRaw('COALESCE(SUM(vp.points),0) as total_point, sc.name as class_name, sc.parallel_name')
                ->orderByDesc('total_point')
                ->limit(3)
                ->get()
                ->map(function ($item) {
                    $kelas = $item->class_name ? ($item->class_name . ($item->parallel_name ? ' - ' . $item->parallel_name : '')) : '-';
                    return (object)[
                        'name' => $item->name,
                        'class_name' => $kelas,
                        'total_point' => $item->total_point
                    ];
                });

            // Hitung total kasus prestasi dan pelanggaran (bukan point)
            $totalAchievementCases = \App\Models\Student::leftJoin('achievements', function ($join) {
                $join->on('achievements.student_id', '=', 'students.nisn')
                    ->where('achievements.validation_status', 'approved');
            })->whereNotNull('achievements.id')->count();

            $totalViolationCases = \App\Models\Student::leftJoin('violations', function ($join) {
                $join->on('violations.student_id', '=', 'students.nisn')
                    ->where('violations.validation_status', 'approved');
            })->whereNotNull('violations.id')->count();

            $totalCases = $totalAchievementCases + $totalViolationCases;
            $achievementPercentage = $totalCases > 0 ? round(($totalAchievementCases / $totalCases) * 100) : 0;
            $violationPercentage = $totalCases > 0 ? round(($totalViolationCases / $totalCases) * 100) : 0;

            return view('home', compact('topAchievementStudents', 'topViolationStudents', 'achievementPercentage', 'violationPercentage'));
        } else if (Auth::user()->hasRole('Siswa')) {
            $user = Auth::user();
            $student = Student::where('user_id', $user->id)->first();
            if (!$student) {
                abort(404, 'Data siswa tidak ditemukan atau belum terhubung ke akun Anda.');
            }

            // Prestasi siswa (hanya yang sudah divalidasi)
            $achievements = $student->achievements()->where('validation_status', 'approved')->get();
            $prestasiTotal = $achievements->sum(function ($a) {
                return optional($a->achievementPoint)->points ?? 0;
            });

            // Pelanggaran siswa (hanya yang sudah divalidasi)
            $violations = $student->violations()->where('validation_status', 'approved')->get();
            $pelanggaranTotal = $violations->sum(function ($v) {
                return optional($v->violationPoint)->points ?? 0;
            });

            // Pie chart data
            $pieData = [
                'prestasi' => $prestasiTotal,
                'pelanggaran' => $pelanggaranTotal
            ];

            // Detail list prestasi
            $prestasiList = $achievements->map(function ($a) {
                return [
                    'name' => $a->achievements_name,
                    'point' => optional($a->achievementPoint)->points ?? 0
                ];
            });
            // Detail list pelanggaran
            $pelanggaranList = $violations->map(function ($v) {
                return [
                    'name' => optional($v->violationPoint)->violation_type ?? '-',
                    'point' => optional($v->violationPoint)->points ?? 0
                ];
            });

            // Performa kehadiran (contoh: per bulan, bisa disesuaikan)
            $attendance = \App\Models\Attendance::where('student_id', $student->nisn)
                ->selectRaw('MONTH(meeting_date) as month, COUNT(*) as total')
                ->groupBy('month')->orderBy('month')->pluck('total', 'month');
            // Dummy data jika belum ada data kehadiran
            $attendance = $attendance->isEmpty() ? collect([1 => 10, 2 => 12, 3 => 8, 4 => 15, 5 => 9, 6 => 11, 7 => 13, 8 => 10, 9 => 14, 10 => 12, 11 => 13, 12 => 9]) : $attendance;

            return view('home', compact('pieData', 'prestasiList', 'pelanggaranList', 'attendance'));
        } else if (Auth::user()->hasRole('Guru')) {
            #// Dashboard Guru
            $selectedDate = request()->date ?? Carbon::today()->toDateString();
            $dayOfWeek = Carbon::parse($selectedDate)->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)
            $userId = Auth::id();
            $teacherId = DB::table('teachers')->where('user_id', $userId)->value('nip');

            $data = DB::table('attendances')
                ->join('class_schedules', 'attendances.class_schedule_id', '=', 'class_schedules.id')
                ->join('classes', 'class_schedules.class_id', '=', 'classes.id')
                ->join('hours', 'class_schedules.hour_id', '=', 'hours.id')
                ->join('teaching_assignments', 'class_schedules.assignment_id', '=', 'teaching_assignments.id')
                ->where('teaching_assignments.teacher_id', $teacherId)
                ->where('class_schedules.day_of_week', $dayOfWeek)
                ->whereDate('attendances.meeting_date', $selectedDate)
                ->select(
                    'hours.slot_number',
                    'hours.start_time',
                    'hours.end_time',
                    'classes.name as class_name',
                    'classes.parallel_name',
                    DB::raw('COUNT(DISTINCT attendances.student_id) as total'),
                    DB::raw("SUM(CASE WHEN LOWER(attendances.status) = 'hadir' THEN 1 ELSE 0 END) as hadir"),
                    DB::raw("SUM(CASE WHEN LOWER(attendances.status) = 'terlambat' THEN 1 ELSE 0 END) as terlambat"),
                    DB::raw("SUM(CASE WHEN LOWER(attendances.status) IN ('abses', 'sakit', 'izin') THEN 1 ELSE 0 END) as tidak_hadir")
                )
                ->groupBy(
                    'hours.slot_number',
                    'hours.start_time',
                    'hours.end_time',
                    'classes.name',
                    'classes.parallel_name'
                )
                ->orderBy('hours.slot_number')
                ->get()
                ->map(function ($item) {
                    $item->jam_pelajaran = "Jam {$item->slot_number} (" .
                        date('H:i', strtotime($item->start_time)) . " - " .
                        date('H:i', strtotime($item->end_time)) . ")";
                    $item->kelas = "{$item->class_name}-{$item->parallel_name}";
                    return $item;
                });

            return view('home', compact('data', 'selectedDate'));
        } else {
            return view('home');
        }
    }

    protected function superadminDashboard()
    {
        // Bagian 1: Pengambilan Filter (Tidak berubah)
        $selectedYear = request()->input('year', Carbon::now()->year);
        $selectedMonth = request()->input('month');

        $startYearRange = Carbon::now()->subYears(5)->year;
        $endYearRange = Carbon::now()->addYear(1)->year;
        $availableYears = range($endYearRange, $startYearRange);

        // Bagian 2: Statistik Kartu (Tidak berubah)
        $userStats = [
            'total_users' => DB::table('users')->whereYear('created_at', $selectedYear)->count(),
            'total_admins' => DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->join('users', 'model_has_roles.model_id', '=', 'users.id')
                ->where('roles.name', 'Admin Sekolah')
                ->whereYear('users.created_at', $selectedYear)->count(),
            'total_teachers' => DB::table('teachers')
                ->join('users', 'teachers.user_id', '=', 'users.id')
                ->whereYear('users.created_at', $selectedYear)->count(),
            'total_students' => DB::table('students')
                ->where('enter_year', '<=', $selectedYear)
                ->count(),
        ];

        // Bagian 3: Log Aktivitas (Tidak berubah)
        $activityLogsQuery = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('users.name as user', 'roles.name as role', DB::raw("'Melakukan aktivitas' as activity"), 'users.updated_at as time')
            ->whereYear('users.updated_at', $selectedYear)
            ->when($selectedMonth, function ($query, $month) {
                return $query->whereMonth('users.updated_at', $month);
            })
            ->orderBy('users.updated_at', 'desc');

        $activityLogs = $activityLogsQuery->get()->map(function ($item) {
            return [
                'user' => $item->user,
                'role' => $item->role,
                'activity' => $item->activity,
                'time' => Carbon::parse($item->time)->format('d.m.Y - h:i A')
            ];
        });

        // Bagian 4: Data Grafik (Perbaikan pada query 'admins')
        $getMonthlyData = function ($queryBuilder, $dateColumn = 'created_at') use ($selectedYear) {
            $data = $queryBuilder->select(DB::raw('MONTH(' . $dateColumn . ') as month'), DB::raw('COUNT(*) as count'))
                ->whereYear($dateColumn, $selectedYear)
                ->groupBy('month')->orderBy('month')->get();

            $monthlyCounts = array_fill(0, 12, 0);
            foreach ($data as $item) {
                $monthlyCounts[$item->month - 1] = $item->count;
            }
            return $monthlyCounts;
        };

        $chartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'datasets' => [
                'users' => $getMonthlyData(DB::table('users')),
                // --- PERBAIKAN DI SINI ---
                // Secara eksplisit memberitahu untuk menggunakan 'users.created_at'
                'admins' => $getMonthlyData(
                    DB::table('users')
                        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                        ->where('roles.name', 'Admin Sekolah'),
                    'users.created_at' // Menambahkan parameter ini untuk kejelasan
                ),
                // --- PERBAIKAN SELESAI ---
                'teachers' => $getMonthlyData(DB::table('teachers')
                    ->join('users', 'teachers.user_id', '=', 'users.id'), 'users.created_at'),
                'students' => $getMonthlyData(DB::table('students'), 'created_at'),
            ]
        ];

        // Bagian 5: Kirim semua data ke view (Tidak berubah)
        return view('home', [
            'userStats' => $userStats,
            'chartData' => $chartData,
            'activityLogs' => $activityLogs,
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears,
            'selectedMonth' => $selectedMonth,
        ]);
    }

    /**
     * Poin Prestasi untuk siswa (role Siswa)
     */
    public function studentAchievements(Request $request)
    {
        $user = Auth::user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();
        $date = $request->input('date');
        $query = $student->achievements()->with('achievementPoint')->where('validation_status', 'approved');
        if ($date) {
            $query->whereDate('achievement_date', $date);
        }
        $achievements = $query->orderBy('achievement_date', 'desc')->get();
        $totalPoint = $achievements->sum(function ($a) {
            return optional($a->achievementPoint)->points ?? 0;
        });
        return view('student.achievements', compact('achievements', 'totalPoint', 'date', 'student'));
    }

    /**
     * Poin Pelanggaran untuk siswa (role Siswa)
     */
    public function studentViolations(Request $request)
    {
        $user = Auth::user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();
        $date = $request->input('date');
        $query = $student->violations()->with('violationPoint')->where('validation_status', 'approved');
        if ($date) {
            $query->whereDate('violation_date', $date);
        }
        $violations = $query->orderBy('violation_date', 'desc')->get();
        $totalPoint = $violations->sum(function ($v) {
            return optional($v->violationPoint)->points ?? 0;
        });
        return view('student.violations', compact('violations', 'totalPoint', 'date', 'student'));
    }
}
