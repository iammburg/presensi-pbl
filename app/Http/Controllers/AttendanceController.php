<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\ClassSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Student;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create_attendance')->only(['create', 'store']);
        $this->middleware('permission:read_attendance')->only(['index', 'show']);
        $this->middleware('permission:update_attendance')->only(['edit', 'update']);
        $this->middleware('permission:delete_attendance')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $today = now()->dayOfWeekIso;

        $allSchedules = ClassSchedule::with(['schoolClass', 'assignment.subject', 'hour'])
            ->where('day_of_week', $today)
            ->whereHas('assignment', function ($query) {
                $query->where('teacher_id', Auth::user()->teacher->nip);
            })
            ->orderBy('hour_id')
            ->get();

        $classSchedules = collect();

        $grouped = $allSchedules->groupBy('class_id');

        foreach ($grouped as $classId => $schedules) {
            $schoolClass = $schedules->first()->schoolClass;
            $className = $schoolClass->name;
            if ($schoolClass->parallel_name) {
                $className .= ' - ' . $schoolClass->parallel_name;
            }

            $subjectName = $schedules->first()->assignment->subject->name;

            $firstStart = $allSchedules->where('class_id', $classId)->min(function ($schedule) {
                return $schedule->hour->start_time;
            });

            $lastEnd = $allSchedules->where('class_id', $classId)->max(function ($schedule) {
                return $schedule->hour->end_time;
            });

            $classSchedules->push((object) [
                'day_of_week'  => $today,
                'class_label'  => $className,
                'subject_name' => $subjectName,
                'start_time'   => $firstStart,
                'end_time'     => $lastEnd,
                'class_id'     => $classId
            ]);
        }

        return view('attendances.index', compact('classSchedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'class_id'    => 'required|integer|exists:classes,id',
            'day_of_week' => 'required|integer|min:1|max:7',
        ]);

        $today = now()->dayOfWeekIso;
        if ($today != $request->day_of_week) {
            abort(403, 'Bukan hari untuk presensi kelas ini.');
        }

        $schedules = ClassSchedule::with(['schoolClass', 'assignment.subject', 'hour'])
            ->where('class_id', $request->class_id)
            ->where('day_of_week', $today)
            ->whereHas('assignment', function ($query) {
                $query->where('teacher_id', Auth::user()->teacher->nip);
            })
            ->orderBy('hour_id')
            ->get();

        if ($schedules->isEmpty()) {
            abort(404, 'Jadwal tidak ditemukan untuk kelas ini.');
        }

        $schoolClass = $schedules->first()->schoolClass;
        $className   = $schoolClass->name
            . ($schoolClass->parallel_name ? ' - ' . $schoolClass->parallel_name : '');

        $subjectName = $schedules->first()->assignment->subject->name;
        $firstStart  = $schedules->min(fn($s) => $s->hour->start_time);
        $lastEnd     = $schedules->max(fn($s) => $s->hour->end_time);
        $graceMinutes = 10;

        return view('attendances.scan', [
            'class_id'     => $request->class_id,
            'class_label'  => $className,
            'subject_name' => $subjectName,
            'start_time'   => Carbon::parse($firstStart)->format('H:i'),
            'end_time'     => Carbon::parse($lastEnd)->format('H:i'),
            'grace_minutes' => $graceMinutes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log::info('AttendanceController@store dipanggil', $request->all());

        $scan = $request->validate([
            'class_schedule_id' => 'required|exists:class_schedules,id',
            'nisn'              => 'required|exists:students,nisn',
            'meeting_date'      => 'required|date',
        ]);

        // Log::info('scan setelah validasi', $scan);

        $exists = Attendance::where('class_schedule_id', $scan['class_schedule_id'])
            ->where('student_id', $scan['nisn'])
            ->where('meeting_date', $scan['meeting_date'])
            ->exists();

        // Log::info('Apakah sudah ada presensi sebelumnya?', ['exists' => $exists]);

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah tercatat presensi untuk siswa ini.'
            ], 409);
        }

        $attendance = Attendance::create([
            'class_schedule_id' => $scan['class_schedule_id'],
            'student_id'        => $scan['nisn'],
            'meeting_date'      => $scan['meeting_date'],
            'time_in'           => now()->format('H:i:s'),
            'status'            => 'Hadir',
            'recorded_by'       => Auth::id(),
        ]);

        // Log::info('Presensi berhasil dibuat', [
        //     'attendance_id' => $attendance->id,
        //     'student'       => $attendance->student->name,
        // ]);

        return response()->json([
            'success' => true,
            'message' => "Presensi {$attendance->student->name} berhasil dicatat.",
        ], 200);
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

    public function updatePhoto(Request $req, Student $student)
    {
        $req->validate(['photo' => 'required|image']);
        $file = $req->file('photo');
        $name = $student->nisn . '_' . time() . '.' . $file->extension();
        $file->storeAs('public/student-photos', $name);

        event(new \App\Events\StudentPhotoUploaded($student->nisn, $file));

        return back()->with('success', 'Foto siswa ter-upload dan embedding terkirim.');
    }
}
