<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\ClassSchedule;

class AttendanceHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create_attendance_history')->only(['create', 'store']);
        $this->middleware('permission:read_attendance_history')->only(['index', 'show']);
        $this->middleware('permission:update_attendance_history')->only(['edit', 'update']);
        $this->middleware('permission:delete_attendance_history')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $classes = SchoolClass::with('academicYear')->get();
        $months  = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        $years = Attendance::selectRaw('YEAR(meeting_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $selectedClass = $request->input('class');
        $selectedMonth = $request->input('month');
        $selectedYear  = $request->input('year', $years->first() ?? date('Y'));

        $students   = collect();
        $percentage = [];
        $showResult = false;

        if ($selectedClass && $selectedMonth && $selectedYear) {
            $showResult = true;

            $studentIds = Attendance::whereYear('meeting_date', $selectedYear)
                ->whereMonth('meeting_date', $selectedMonth)
                ->whereHas(
                    'classSchedule',
                    fn($q) =>
                    $q->where('class_id', $selectedClass)
                )
                ->pluck('student_id')
                ->unique();

            $students = Student::whereIn('nisn', $studentIds)->get();

            foreach ($students as $student) {
                $baseQuery = Attendance::where('student_id', $student->nisn)
                    ->whereYear('meeting_date', $selectedYear)
                    ->whereMonth('meeting_date', $selectedMonth)
                    ->whereHas(
                        'classSchedule',
                        fn($q) =>
                        $q->where('class_id', $selectedClass)
                    );

                $total = $baseQuery->count();
                $hadir = (clone $baseQuery)
                    ->where('status', 'Hadir')
                    ->count();

                $percentage[$student->nisn] = $total > 0
                    ? round($hadir / $total * 100)
                    : 0;
            }
        }

        return view('attendances.history', compact(
            'classes',
            'months',
            'years',
            'selectedClass',
            'selectedMonth',
            'selectedYear',
            'students',
            'percentage',
            'showResult'
        ));
    }

    /**
     * Route to detail page from history page
     */
    public function redirectToDetail(Request $request)
    {
        return redirect()->route('attendances.history-detail', [
            'class' => $request->class,
            'month' => $request->month,
            'year' => $request->year,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }

    /**
     * Show the detail attendance recap for a class, month, year, and date.
     */
    public function detail(Request $request)
    {
        $classes = SchoolClass::with('academicYear')->get();
        $months  = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        $years = Attendance::selectRaw('YEAR(meeting_date) as year')
            ->distinct()->orderByDesc('year')->pluck('year');

        $selectedClass = $request->input('class');
        $selectedMonth = $request->input('month');
        $selectedYear  = $request->input('year');
        $selectedDate  = $request->input('date');

        $dates = collect();
        $students = collect();
        $attendances = collect();
        $showResult = false;
        $studentStats = [];
        $teacherAttendance = null;

        if ($selectedClass && $selectedMonth && $selectedYear) {
            $dates = Attendance::whereYear('meeting_date', $selectedYear)
                ->whereMonth('meeting_date', $selectedMonth)
                ->whereHas('classSchedule', fn($q) => $q->where('class_id', $selectedClass))
                ->orderBy('meeting_date')
                ->pluck('meeting_date')->unique();

            if (!$selectedDate && $dates->count() > 0) {
                $selectedDate = $dates->first();
            }

            if ($selectedDate) {
                $showResult = true;
                $studentIds = Attendance::whereDate('meeting_date', $selectedDate)
                    ->whereHas('classSchedule', fn($q) => $q->where('class_id', $selectedClass))
                    ->pluck('student_id')->unique();
                $students = Student::whereIn('nisn', $studentIds)->get();
                $attendances = Attendance::whereDate('meeting_date', $selectedDate)
                    ->whereHas('classSchedule', fn($q) => $q->where('class_id', $selectedClass))
                    ->get()->keyBy('student_id');

                $statusCounts = Attendance::whereDate('meeting_date', $selectedDate)
                    ->whereHas('classSchedule', fn($q) => $q->where('class_id', $selectedClass))
                    ->whereIn('student_id', $studentIds)
                    ->select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->pluck('total', 'status')->toArray();
                $totalStudents = count($studentIds);
                $studentStats = [];
                foreach (["Hadir", "Absen", "Sakit", "Izin", "Terlambat"] as $status) {
                    $studentStats[$status] = $totalStudents > 0 ? round(($statusCounts[$status] ?? 0) / $totalStudents * 100, 2) : 0;
                }

                $classSchedule = ClassSchedule::where('class_id', $selectedClass)
                    ->whereHas('attendances', function ($q) use ($selectedDate) {
                        $q->whereDate('meeting_date', $selectedDate);
                    })->first();
                $teacherName = null;
                $teacherStatus = null;
                if ($classSchedule) {
                    $teacher = $classSchedule->teacher;
                    $teacherName = $teacher ? $teacher->name : null;
                    $teacherAttendanceRow = Attendance::where('class_schedule_id', $classSchedule->id)
                        ->whereDate('meeting_date', $selectedDate)
                        ->whereNull('student_id')
                        ->first();
                    if ($teacherAttendanceRow) {
                        $teacherStatus = $teacherAttendanceRow->status;
                    }
                }
                $teacherAttendance = [
                    'name' => $teacherName,
                    'status' => $teacherStatus,
                ];
            }
        }

        return view('attendances.history-detail', compact(
            'classes',
            'months',
            'years',
            'selectedClass',
            'selectedMonth',
            'selectedYear',
            'selectedDate',
            'dates',
            'students',
            'attendances',
            'showResult',
            'studentStats',
            'teacherAttendance',
        ));
    }
}
