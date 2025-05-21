<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Hour;
use App\Models\Teacher;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
{
    $search = $request->input('search', '');
    $perPage = $request->input('perPage', 10);

    $classIds = ClassSchedule::when($search, function ($query) use ($search) {
            $query->whereHas('SchoolClass', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('subject', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('teacher', fn($q) => $q->where('name', 'like', "%{$search}%"));
        })
        ->select('class_id')
        ->groupBy('class_id')
        ->pluck('class_id');

    $schedules = ClassSchedule::with(['SchoolClass'])
        ->whereIn('class_id', $classIds)
        ->groupBy('class_id') // memastikan tidak dobel
        ->selectRaw('MIN(id) as id') // ambil salah satu id jadwal tiap kelas
        ->orderByDesc('id')
        ->paginate($perPage);

    // Ambil data lengkap berdasarkan id dari hasil group
    $schedules = ClassSchedule::with(['SchoolClass', 'subject', 'teacher'])
        ->whereIn('id', $schedules->pluck('id'))
        ->paginate($perPage);

    return view('class_schedule.index', compact('schedules', 'search'));
}
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        // Ambil daftar kelas, urutkan berdasarkan nama
        $classes = SchoolClass::orderBy('name')->get(); // Changed from Classes to SchoolClass

        // Ambil daftar mapel
        $subjects = Subject::orderBy('name')->get();

        // Ambil daftar jam pelajaran/istirahat
        $hours = Hour::orderBy('start_time')->get();

        // Ambil daftar pengampu (relasi guru dan mapel)
        $teachingAssignments = TeachingAssignment::with(['teacher', 'subject'])->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'teacher_id' => $item->teacher_id,
                    'teacher_name' => $item->teacher->name ?? '',
                    'subject_id' => $item->subject_id,
                    'subject_name' => $item->subject->name ?? '',
                ];
            });

        return view('class_schedule.create', compact(
            'classes',
            'subjects',
            'hours',
            'teachingAssignments'
        ));
    }


public function store(Request $request)
{
    $validated = $request->validate([
        'semester' => 'required|string',
        'class_id' => 'required|exists:classes,id',
        'schedules' => 'required|array',
        'schedules.*.*.session_type' => 'required|string|in:Jam Pelajaran,Jam Istirahat',
        'schedules.*.*.start_hour_id' => 'required|exists:hours,id',
        'schedules.*.*.end_hour_id' => 'required|exists:hours,id',
        'schedules.*.*.assignment_id' => 'nullable|exists:teaching_assignments,id',
    ]);

    $classId = $validated['class_id'];
    $schedules = $request->input('schedules', []);

    $dayMapping = [
        'Senin'  => 1,
        'Selasa' => 2,
        'Rabu'   => 3,
        'Kamis'  => 4,
        'Jumat'  => 5,
    ];

    DB::beginTransaction();
    try {
        foreach ($schedules as $day => $entries) {
            $dayNumber = $dayMapping[$day] ?? null;

            foreach ($entries as $entry) {
                $start = (int) $entry['start_hour_id'];
                $end   = (int) $entry['end_hour_id'];
                $sessionType = $entry['session_type'];

                // Set null jika Jam Istirahat, pastikan valid jika Jam Pelajaran
                $assignmentId = $sessionType === 'Jam Istirahat'
                    ? null
                    : ($entry['assignment_id'] ?? null);

                // Validasi manual untuk Jam Pelajaran tanpa assignment
                if ($sessionType === 'Jam Pelajaran' && !$assignmentId) {
                    throw new \Exception("Assignment tidak boleh kosong untuk sesi Jam Pelajaran pada hari $day.");
                }

                for ($hourId = $start; $hourId <= $end; $hourId++) {
                    ClassSchedule::create([
                        'class_id'      => $classId,
                        'assignment_id' => $assignmentId,
                        'day_of_week'   => $dayNumber,
                        'hour_id'       => $hourId,
                    ]);
                }
            }
        }

        DB::commit();
        return redirect()->route('manage-schedules.index')
            ->with('success', 'Jadwal berhasil disimpan.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage());
    }
}


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($class_id)
{
    $class = SchoolClass::findOrFail($class_id);

    $schedules = ClassSchedule::with(['hour', 'assignment.subject', 'assignment.teacher'])
        ->where('class_id', $class_id)
        ->get();

    return view('class_schedule.show', compact('class', 'schedules'));
}


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $schedule = ClassSchedule::findOrFail($id);
        $classes = SchoolClass::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $teachers = TeachingAssignment::with('teacher')
        ->select('teacher_id')
        ->distinct()
        ->get()
        ->pluck('teacher');

        return view('class_schedule.edit', compact('schedule', 'classes', 'subjects', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,nip',
            'day' => 'required|string|max:20',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time'
        ]);

        $schedule = ClassSchedule::findOrFail($id);

        // Check for schedule conflicts (excluding this record)
        $conflicts = ClassSchedule::where('id', '!=', $id)
            ->where('class_id', $validated['class_id'])
            ->where('day', $validated['day'])
            ->where(function($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhere(function($q) use ($validated) {
                        $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                    });
            })
            ->exists();

        if ($conflicts) {
            return redirect()->back()->withInput()->with('error', 'Jadwal bentrok dengan jadwal yang sudah ada di kelas ini pada hari dan waktu yang sama.');
        }

        // Check teacher availability (excluding this record)
        $teacherConflicts = ClassSchedule::where('id', '!=', $id)
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('teacher_id', $validated['teacher_id'])
            ->where('day', $validated['day'])
            ->where(function($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhere(function($q) use ($validated) {
                        $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                    });
            })
            ->exists();

        if ($teacherConflicts) {
            return redirect()->back()->withInput()->with('error', 'Guru sudah memiliki jadwal pada hari dan waktu yang sama.');
        }

        $schedule->update($validated);

        return redirect()->route('manage-schedules.index')->with('success', 'Jadwal berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $schedule = ClassSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('manage-schedules.index')->with('success', 'Jadwal berhasil dihapus!');
    }
}