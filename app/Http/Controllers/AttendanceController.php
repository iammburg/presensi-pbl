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
use Yajra\DataTables\DataTables;
use Illuminate\Support\Arr;

class AttendanceController extends Controller
{
    // Static variable untuk deadline presensi (dalam menit)
    const ATTENDANCE_DEADLINE_MINUTES = 20;

    public function __construct()
    {
        $this->middleware('permission:create_attendance')->only(['create', 'store']);
        $this->middleware('permission:read_attendance')->only(['index', 'show', 'showByClass']);
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

            $subjectName = $schedules->first()->assignment->subject->subject_name;

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

        $scheduleIds = $schedules->pluck('id');

        Log::info('Create method data', [
            'class_id' => $request->class_id,
            'schedules_count' => $schedules->count(),
            'schedule_ids' => $scheduleIds->toArray()
        ]);

        if ($schedules->isEmpty()) {
            abort(404, 'Jadwal tidak ditemukan untuk kelas ini.');
        }

        $schoolClass = $schedules->first()->schoolClass;
        $className   = $schoolClass->name
            . ($schoolClass->parallel_name ? ' - ' . $schoolClass->parallel_name : '');

        $subjectName = $schedules->first()->assignment->subject->subject_name;
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
            'scheduleIds'  => $scheduleIds->toArray(),
        ]);
    }

    /**
     * Determine attendance status based on time
     */
    private function determineAttendanceStatus($classScheduleId, $currentTime = null)
    {
        if (!$currentTime) {
            $currentTime = now();
        }

        // Get the class schedule to determine start time
        $classSchedule = ClassSchedule::with('hour')->find($classScheduleId);

        if (!$classSchedule) {
            return 'Hadir'; // Default jika tidak ada jadwal
        }

        $startTime = Carbon::parse($classSchedule->hour->start_time);
        $deadlineTime = $startTime->copy()->addMinutes(self::ATTENDANCE_DEADLINE_MINUTES);

        // Convert current time to today with the attendance time
        $attendanceTime = Carbon::parse($currentTime->format('H:i:s'));

        Log::info('Attendance time check', [
            'start_time' => $startTime->format('H:i:s'),
            'deadline_time' => $deadlineTime->format('H:i:s'),
            'attendance_time' => $attendanceTime->format('H:i:s'),
            'is_late' => $attendanceTime->gt($deadlineTime)
        ]);

        // Jika presensi setelah deadline, maka status "Terlambat"
        if ($attendanceTime->gt($deadlineTime)) {
            return 'Terlambat';
        }

        return 'Hadir';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Tambahkan debugging
        Log::info('AttendanceController@store dipanggil', [
            'request_data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        try {
            $scan = $request->validate([
                'class_schedule_id' => 'required|exists:class_schedules,id',
                'nisn'              => 'required|exists:students,nisn',
                'meeting_date'      => 'required|date',
            ]);

            Log::info('Validasi berhasil', $scan);

            $exists = Attendance::where('class_schedule_id', $scan['class_schedule_id'])
                ->where('student_id', $scan['nisn'])
                ->where('meeting_date', $scan['meeting_date'])
                ->exists();

            // Log::info('Apakah sudah ada presensi sebelumnya?', ['exists' => $exists]);

            if ($exists) {
                // Log::warning('Presensi sudah ada untuk siswa ini', $scan);
                $student = Student::where('nisn', $scan['nisn'])->first();
                return response()->json([
                    'success' => false,
                    'message' => "Sudah tercatat presensi untuk siswa {$student->name} (NISN: {$scan['nisn']}).",
                ], 409);
            }

            // Tentukan status berdasarkan waktu presensi
            $attendanceStatus = $this->determineAttendanceStatus($scan['class_schedule_id']);
            $currentTime = now();

            $attendance = Attendance::create([
                'class_schedule_id' => $scan['class_schedule_id'],
                'student_id'        => $scan['nisn'],
                'meeting_date'      => $scan['meeting_date'],
                'time_in'           => $currentTime->format('H:i:s'),
                'status'            => $attendanceStatus,
                'recorded_by'       => Auth::id(),
            ]);

            Log::info('Presensi berhasil dibuat', [
                'attendance_id' => $attendance->id,
                'student'       => $attendance->student->name,
                'status'        => $attendanceStatus,
                'time_in'       => $currentTime->format('H:i:s'),
            ]);

            $statusMessage = $attendanceStatus === 'Terlambat'
                ? "Presensi {$attendance->student->name} tercatat sebagai TERLAMBAT."
                : "Presensi {$attendance->student->name} berhasil dicatat.";

            return response()->json([
                'success' => true,
                'message' => $statusMessage,
                'status' => $attendanceStatus,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                // 'message' => 'Data tidak valid: ' . implode(', ', Arr::flatten($e->errors())),
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan presensi', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                // 'message' => 'Terjadi kesalahan input: ' . $e->getMessage(),
                'message' => 'Terjadi kesalahan input',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $classScheduleId)
    {
        //
    }

    public function showByClass(Request $request, $classId)
    {
        Log::info('showByClass called', [
            'classId' => $classId,
            'isAjax' => $request->ajax(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl()
        ]);

        $schedules = ClassSchedule::where('class_id', $classId)
            ->where('day_of_week', now()->dayOfWeekIso)
            ->whereHas('assignment', fn($q) => $q->where('teacher_id', Auth::user()->teacher->nip))
            ->get();

        abort_if($schedules->isEmpty(), 404, 'Jadwal tidak ditemukan.');

        $classSchedule = $schedules->first();

        if ($request->ajax()) {
            $students = Student::whereHas('classAssignments', function ($query) use ($classId) {
                $query->where('class_id', $classId);
            })->get();

            Log::info('Students found', ['count' => $students->count()]);

            $attendances = Attendance::whereIn('class_schedule_id', $schedules->pluck('id'))
                ->where('meeting_date', today()->toDateString())
                ->get()
                ->keyBy('student_id');

            Log::info('Attendances found', ['count' => $attendances->count()]);

            return datatables()->of($students)
                ->addIndexColumn() // Kolom penomoran otomatis
                ->addColumn('name', function ($student) {
                    return $student->name;
                })
                ->addColumn('status', function ($student) use ($attendances) {
                    $attendance = $attendances->get($student->nisn); // Gunakan NISN sebagai key
                    $status = $attendance ? $attendance->status : 'Absen';

                    $select = '<select name="statuses[' . $student->nisn . ']" class="form-control attendance-status">';
                    $options = ['Absen', 'Hadir', 'Sakit', 'Izin', 'Terlambat'];
                    foreach ($options as $option) {
                        $selected = $status === $option ? 'selected' : '';
                        $select .= '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                    }
                    $select .= '</select>';

                    return $select;
                })
                ->addColumn('time_in', function ($student) use ($attendances) {
                    $attendance = $attendances->get($student->nisn);
                    if ($attendance && $attendance->time_in) {
                        return \Carbon\Carbon::parse($attendance->time_in)->format('H:i');
                    }
                    return '-';
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        // Calculate start and end times for the class schedules
        $firstStart = $schedules->min(fn($s) => $s->hour->start_time);
        $lastEnd = $schedules->max(fn($s) => $s->hour->end_time);

        // Log::info('Returning view (not AJAX)', ['classSchedule' => $classSchedule->id]);
        return view('attendances.detail', compact('classSchedule', 'firstStart', 'lastEnd'));
    }

    public function updateStatus(Request $request)
    {
        try {
            $request->validate([
                'class_schedule_id' => 'required|exists:class_schedules,id',
                'statuses' => 'required|array',
                'statuses.*' => 'required|in:Absen,Hadir,Sakit,Izin,Terlambat',
            ]);

            Log::info('updateStatus called', [
                'class_schedule_id' => $request->class_schedule_id,
                'statuses' => $request->statuses
            ]);

            $changedToLate = [];

            foreach ($request->statuses as $studentId => $status) {
                $attendance = Attendance::where('student_id', $studentId)
                    ->where('class_schedule_id', $request->class_schedule_id)
                    ->where('meeting_date', today()->toDateString())
                    ->first();

                $originalStatus = $status;

                if ($attendance) {
                    // Jika status diubah menjadi "Hadir", cek apakah sudah melewati deadline
                    if ($status === 'Hadir') {
                        $actualStatus = $this->determineAttendanceStatus($request->class_schedule_id);
                        if ($actualStatus === 'Terlambat') {
                            $student = Student::where('nisn', $studentId)->first();
                            $changedToLate[] = $student->name;
                        }
                        $status = $actualStatus; // Override dengan status yang sebenarnya
                    }

                    $attendance->update([
                        'status' => $status,
                        'time_in' => in_array($status, ['Hadir', 'Terlambat']) ? ($attendance->time_in ?? now()->format('H:i:s')) : null
                    ]);
                    Log::info('Updated attendance', ['student_id' => $studentId, 'status' => $status]);
                } else {
                    // Hanya buat attendance baru jika bukan status "Absen"
                    if ($status !== 'Absen') {
                        // Jika status yang dipilih "Hadir", cek deadline
                        if ($status === 'Hadir') {
                            $actualStatus = $this->determineAttendanceStatus($request->class_schedule_id);
                            if ($actualStatus === 'Terlambat') {
                                $student = Student::where('nisn', $studentId)->first();
                                $changedToLate[] = $student->name;
                            }
                            $status = $actualStatus; // Override dengan status yang sebenarnya
                        }

                        Attendance::create([
                            'student_id'        => $studentId,
                            'class_schedule_id' => $request->class_schedule_id,
                            'meeting_date'      => today()->toDateString(),
                            'status'            => $status,
                            'time_in'           => in_array($status, ['Hadir', 'Terlambat']) ? now()->format('H:i:s') : null,
                            'recorded_by'       => Auth::id(),
                        ]);
                        Log::info('Created new attendance', ['student_id' => $studentId, 'status' => $status]);
                    }
                }
            }

            // Buat pesan yang informatif
            $message = 'Status presensi berhasil diperbarui.';
            if (!empty($changedToLate)) {
                $names = implode(', ', $changedToLate);
                $message .= ' Catatan: ' . $names . ' diubah menjadi "Terlambat" karena sudah melewati batas waktu presensi.';
            }

            // Return JSON response for AJAX, redirect for regular form submission
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ], 200);
            }

            return back()->with('success', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in updateStatus', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors());
        } catch (\Throwable $th) {
            Log::error('Error updating attendance status', [
                'error_message' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'request' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui status presensi.',
                ], 500);
            }

            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui status presensi: ' . $th->getMessage()]);
        }
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
