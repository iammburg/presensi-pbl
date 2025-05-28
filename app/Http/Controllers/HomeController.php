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

        // Dashboard Guru (tidak diubah)
        $selectedDate = request()->date ?? Carbon::today()->toDateString();
        $userId = Auth::id();
        $teacherId = DB::table('teachers')->where('user_id', $userId)->value('nip');

        $data = DB::table('attendances')
            ->join('class_schedules', 'attendances.class_schedule_id', '=', 'class_schedules.id')
            ->join('classes', 'class_schedules.class_id', '=', 'classes.id')
            ->join('hours', 'class_schedules.hour_id', '=', 'hours.id')
            ->join('teaching_assignments', 'class_schedules.assignment_id', '=', 'teaching_assignments.id')
            ->where('teaching_assignments.teacher_id', $teacherId)
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
            ->groupBy('hours.slot_number', 'hours.start_time', 'hours.end_time', 'classes.name', 'classes.parallel_name')
            ->orderBy('hours.slot_number')
            ->get()
            ->map(function ($item) {
                $item->jam_pelajaran = "Jam {$item->slot_number} (" . date('H:i', strtotime($item->start_time)) . " - " . date('H:i', strtotime($item->end_time)) . ")";
                $item->kelas = "{$item->class_name}-{$item->parallel_name}";
                return $item;
            });

        return view('home', compact('data', 'selectedDate'));
    }

    protected function superadminDashboard()
    {
        // Get user statistics from database
        $userStats = [
            'total_users' => DB::table('users')->count(),
            'total_admins' => DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('roles.name', 'Admin Sekolah')->count(),
            'total_teachers' => DB::table('teachers')->count(),
            'total_students' => DB::table('students')->count(),
            'active_users' => 0, // kolom belum tersedia
            'inactive_users' => 0,
        ];


        // Get activity logs from database (assuming you have an activity_logs table)
        $activityLogs = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select(
                'users.name as user',
                'roles.name as role',
                DB::raw("'Melakukan aktivitas' as activity"), // Placeholder - replace with actual activity column if available
                'users.updated_at as time'
            )
            ->orderBy('users.updated_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                return [
                    'user' => $item->user,
                    'role' => $item->role,
                    'activity' => $item->activity,
                    'time' => Carbon::parse($item->time)->format('d.m.Y - h:i A')
                ];
            });

        // Get monthly user data for chart
        $monthlyData = DB::table('users')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $chartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'current_year' => array_fill(0, 12, 0),
        ];

        foreach ($monthlyData as $data) {
            $chartData['current_year'][$data->month - 1] = $data->count;
        }

        return view('home', [
            'userStats' => $userStats,
            'chartData' => $chartData,
            'activityLogs' => $activityLogs,
        ]);
    }
}