<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (auth()->user()->hasRole('superadmin')) {
            return $this->superadminDashboard();
        }

        // Dashboard Guru
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
    }



// HomeController.php

// HomeController.php

// HomeController.php

// HomeController.php

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
                'user' => $item->user, 'role' => $item->role,
                'activity' => $item->activity, 'time' => Carbon::parse($item->time)->format('d.m.Y - h:i A')
            ];
        });

        // Bagian 4: Data Grafik (Perbaikan pada query 'admins')
        $getMonthlyData = function ($queryBuilder, $dateColumn = 'created_at') use ($selectedYear) {
            $data = $queryBuilder->select(DB::raw('MONTH('.$dateColumn.') as month'), DB::raw('COUNT(*) as count'))
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
}