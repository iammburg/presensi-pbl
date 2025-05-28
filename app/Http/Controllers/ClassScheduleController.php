<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Hour;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;


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

        // Base query
        $query = ClassSchedule::query();

        // Filter jika ada pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('schoolClass', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('subject', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('teacher', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Ambil class_id
        $classIds = $query->select('class_id')->groupBy('class_id')->pluck('class_id');

        // Ambil schedule id per class_id
        $scheduleIds = ClassSchedule::whereIn('class_id', $classIds)
            ->groupBy('class_id')
            ->selectRaw('MIN(id) as id')
            ->orderByDesc('id')
            ->pluck('id');

        // Final result with eager loading - UBAH DISINI: load academicYear dari schoolClass
        $schedules = ClassSchedule::with([
                'schoolClass.academicYear', // Changed from 'assignment.academicYear'
                'subject',
                'teacher',
                'assignment'
            ])
            ->whereIn('id', $scheduleIds)
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
        // Ambil daftar kelas dengan academic year, urutkan berdasarkan nama
        $classes = SchoolClass::with('academicYear')->orderBy('name')->get();

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
    public function show(ClassSchedule $manage_schedule)
    {
        // Ambil semua jadwal untuk kelas yang sama dengan academic year dari class
        $allSchedules = ClassSchedule::with([
                'assignment.subject', 
                'assignment.teacher', 
                'hour', 
                'schoolClass.academicYear' // Changed from just 'schoolClass'
            ])
            ->where('class_id', $manage_schedule->class_id)
            ->orderBy('day_of_week')
            ->orderBy('hour_id')
            ->get();

        $days = [
            1 => 'Senin',
            2 => 'Selasa', 
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat'
        ];

        // Group schedules by day and merge consecutive hours
        $schedulesPerDay = [];
        
        foreach ($days as $dayNumber => $dayName) {
            $daySchedules = $allSchedules->where('day_of_week', $dayNumber);
            $groupedSchedules = [];
            
            if ($daySchedules->isNotEmpty()) {
                $currentGroup = null;
                
                // Urutkan berdasarkan start_time, bukan slot_number
                foreach ($daySchedules->sortBy('hour.start_time') as $schedule) {
                    $sessionType = $schedule->assignment_id ? 'Jam Pelajaran' : 'Jam Istirahat';
                    $assignmentId = $schedule->assignment_id;
                    
                    // Cek apakah bisa digabung dengan group sebelumnya
                    if ($currentGroup && 
                        $currentGroup['session_type'] === $sessionType && 
                        $currentGroup['assignment_id'] === $assignmentId &&
                        $currentGroup['end_time'] === $schedule->hour->start_time) {
                        
                        // Gabungkan dengan group sebelumnya
                        $currentGroup['end_hour_id'] = $schedule->hour_id;
                        $currentGroup['end_hour_slot'] = $schedule->hour->slot_number;
                        $currentGroup['end_time'] = $schedule->hour->end_time ?? $currentGroup['end_time'];
                        
                        // Tambahkan waktu individual untuk setiap jam
                        $currentGroup['hour_times'][$schedule->hour->slot_number] = $schedule->hour->start_time;
                        $currentGroup['hour_end_times'][$schedule->hour->slot_number] = $schedule->hour->end_time;
                        $currentGroup['hour_schedules'][$schedule->hour->slot_number] = $schedule;
                    } else {
                        // Simpan group sebelumnya jika ada
                        if ($currentGroup) {
                            $groupedSchedules[] = $currentGroup;
                        }
                        
                        // Buat group baru
                        $currentGroup = [
                            'session_type' => $sessionType,
                            'start_hour_id' => $schedule->hour_id,
                            'end_hour_id' => $schedule->hour_id,
                            'start_hour_slot' => $schedule->hour->slot_number,
                            'end_hour_slot' => $schedule->hour->slot_number,
                            'assignment_id' => $assignmentId,
                            'subject_name' => $schedule->assignment->subject->name ?? null,
                            'teacher_name' => $schedule->assignment->teacher->name ?? null,
                            'start_time' => $schedule->hour->start_time ?? null,
                            'end_time' => $schedule->hour->end_time ?? null,
                            // Tambahkan array untuk menyimpan waktu individual setiap jam
                            'hour_times' => [
                                $schedule->hour->slot_number => $schedule->hour->start_time
                            ],
                            'hour_end_times' => [
                                $schedule->hour->slot_number => $schedule->hour->end_time
                            ],
                            'hour_schedules' => [
                                $schedule->hour->slot_number => $schedule
                            ]
                        ];
                    }
                }
                
                // Jangan lupa simpan group terakhir
                if ($currentGroup) {
                    $groupedSchedules[] = $currentGroup;
                }
            }
            
            $schedulesPerDay[$dayName] = $groupedSchedules;
        }

        return view('class_schedule.show', [
            'schedule' => $manage_schedule,
            'schedulesPerDay' => $schedulesPerDay,
            'days' => array_values($days),
            'class' => $manage_schedule->schoolClass
        ]);
    }

public function exportPdf(ClassSchedule $manage_schedule)
{
    try {
        // Debug: Log informasi dasar
        Log::info('Export PDF Debug', [
            'schedule_id' => $manage_schedule->id,
            'class_id' => $manage_schedule->class_id,
        ]);

        // Ambil semua jadwal untuk kelas yang sama
        $allSchedules = ClassSchedule::with([
                'assignment.subject', 
                'assignment.teacher', 
                'hour', 
                'schoolClass.academicYear'
            ])
            ->where('class_id', $manage_schedule->class_id)
            ->orderBy('day_of_week')
            ->orderBy('hour_id')
            ->get();

        // Debug: Log jumlah schedules yang ditemukan
        Log::info('Total schedules found', [
            'count' => $allSchedules->count(),
        ]);

        $days = [
            1 => 'Senin',
            2 => 'Selasa', 
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat'
        ];

        // GUNAKAN LOGIKA YANG SAMA SEPERTI SHOW METHOD
        $schedulesPerDay = [];
        
        foreach ($days as $dayNumber => $dayName) {
            $daySchedules = $allSchedules->where('day_of_week', $dayNumber);
            $groupedSchedules = [];
            
            if ($daySchedules->isNotEmpty()) {
                $currentGroup = null;
                
                // Urutkan berdasarkan start_time, bukan slot_number - SAMA SEPERTI SHOW
                foreach ($daySchedules->sortBy('hour.start_time') as $schedule) {
                    $sessionType = $schedule->assignment_id ? 'Jam Pelajaran' : 'Jam Istirahat';
                    $assignmentId = $schedule->assignment_id;
                    
                    // KONDISI PENGGABUNGAN YANG SAMA SEPERTI SHOW METHOD
                    if ($currentGroup && 
                        $currentGroup['session_type'] === $sessionType && 
                        $currentGroup['assignment_id'] === $assignmentId &&
                        $currentGroup['end_time'] === $schedule->hour->start_time) {
                        
                        // Gabungkan dengan group sebelumnya
                        $currentGroup['end_hour_id'] = $schedule->hour_id;
                        $currentGroup['end_hour_slot'] = $schedule->hour->slot_number;
                        $currentGroup['end_time'] = $schedule->hour->end_time ?? $currentGroup['end_time'];
                        
                        // Tambahkan waktu individual untuk setiap jam
                        $currentGroup['hour_times'][$schedule->hour->slot_number] = $schedule->hour->start_time;
                        $currentGroup['hour_end_times'][$schedule->hour->slot_number] = $schedule->hour->end_time;
                        $currentGroup['hour_schedules'][$schedule->hour->slot_number] = $schedule;
                    } else {
                        // Simpan group sebelumnya jika ada
                        if ($currentGroup) {
                            $groupedSchedules[] = $currentGroup;
                        }
                        
                        // Buat group baru - SAMA SEPERTI SHOW METHOD
                        $currentGroup = [
                            'session_type' => $sessionType,
                            'start_hour_id' => $schedule->hour_id,
                            'end_hour_id' => $schedule->hour_id,
                            'start_hour_slot' => $schedule->hour->slot_number,
                            'end_hour_slot' => $schedule->hour->slot_number,
                            'assignment_id' => $assignmentId,
                            'subject_name' => $schedule->assignment->subject->name ?? null,
                            'teacher_name' => $schedule->assignment->teacher->name ?? null,
                            'start_time' => $schedule->hour->start_time ?? null,
                            'end_time' => $schedule->hour->end_time ?? null,
                            // Tambahkan array untuk menyimpan waktu individual setiap jam
                            'hour_times' => [
                                $schedule->hour->slot_number => $schedule->hour->start_time
                            ],
                            'hour_end_times' => [
                                $schedule->hour->slot_number => $schedule->hour->end_time
                            ],
                            'hour_schedules' => [
                                $schedule->hour->slot_number => $schedule
                            ]
                        ];
                    }
                }
                
                // Jangan lupa simpan group terakhir
                if ($currentGroup) {
                    $groupedSchedules[] = $currentGroup;
                }
            }
            
            $schedulesPerDay[$dayName] = $groupedSchedules;
        }

        // Debug: Log final result sebelum generate PDF
        Log::info('Final schedulesPerDay for PDF', [
            'data' => $schedulesPerDay,
            'has_data' => !empty(array_filter($schedulesPerDay))
        ]);

        // Pastikan ada data sebelum generate PDF
        if (empty(array_filter($schedulesPerDay))) {
            Log::warning('No schedule data found for PDF export');
            return redirect()->back()->with('error', 'Tidak ada data jadwal untuk diekspor.');
        }

        $pdf = Pdf::loadView('class_schedule.pdf', [
            'schedule' => $manage_schedule,
            'schedulesPerDay' => $schedulesPerDay,
            'days' => array_values($days),
            'class' => $manage_schedule->schoolClass
        ])->setPaper('a4', 'landscape');

        return $pdf->download('jadwal_kelas_' . optional($manage_schedule->schoolClass)->name . '.pdf');

    } catch (\Exception $e) {
        Log::error('Gagal export PDF', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()->with('error', 'Terjadi kesalahan saat export PDF: ' . $e->getMessage());
    }
}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  ClassSchedule  $manageSchedule
     * @return \Illuminate\Http\Response
     */
    public function edit(ClassSchedule $manageSchedule)
    {
        $class_id = $manageSchedule->class_id;
        $class = SchoolClass::with('academicYear')->findOrFail($class_id); // Load academic year

        // Ambil semua jadwal untuk kelas ini
        $existingSchedules = ClassSchedule::with(['hour', 'assignment.subject', 'assignment.teacher'])
            ->where('class_id', $class_id)
            ->get()
            ->groupBy('day_of_week');

        // Konversi jadwal yang ada ke format yang sesuai dengan form
        $dayMap = [
            1 => 'Senin',
            2 => 'Selasa', 
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
        ];

        // Proses data jadwal untuk form
        $scheduleData = [];
        foreach ($existingSchedules as $dayNumber => $daySchedules) {
            $dayName = $dayMap[$dayNumber];
            $scheduleData[$dayName] = [];
            
            // Group consecutive hours with same assignment
            $groupedSchedules = [];
            $currentGroup = null;
            
            foreach ($daySchedules->sortBy('hour.slot_number') as $schedule) {
                $sessionType = $schedule->assignment_id ? 'Jam Pelajaran' : 'Jam Istirahat';
                $assignmentId = $schedule->assignment_id;
                
                if ($currentGroup && 
                    $currentGroup['session_type'] === $sessionType && 
                    $currentGroup['assignment_id'] === $assignmentId &&
                    $currentGroup['end_hour_id'] + 1 === $schedule->hour_id) {
                    // Extend current group
                    $currentGroup['end_hour_id'] = $schedule->hour_id;
                } else {
                    // Start new group
                    if ($currentGroup) {
                        $groupedSchedules[] = $currentGroup;
                    }
                    $currentGroup = [
                        'session_type' => $sessionType,
                        'start_hour_id' => $schedule->hour_id,
                        'end_hour_id' => $schedule->hour_id,
                        'assignment_id' => $assignmentId,
                        'hour_session_type' => $schedule->hour->session_type ?? $sessionType, // tambahkan ini
                    ];
                }
            }
            
            if ($currentGroup) {
                $groupedSchedules[] = $currentGroup;
            }
            
            $scheduleData[$dayName] = $groupedSchedules;
        }

        // Data untuk form (sama seperti create) - load academic year
        $classes = SchoolClass::with('academicYear')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $hours = Hour::orderBy('start_time')->get();
        
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

        // Default semester (bisa disesuaikan logic bisnis)
        $semester = '1'; // atau ambil dari database jika ada field semester

        return view('class_schedule.edit', compact(
            'class', 'classes', 'subjects', 'hours', 'teachingAssignments', 
            'scheduleData', 'manageSchedule', 'semester'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ClassSchedule  $manageSchedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClassSchedule $manageSchedule)
    {
        $class_id = $manageSchedule->class_id;

        $validated = $request->validate([
            'semester' => 'required|string',
            'class_id' => 'required|exists:classes,id',
            'schedules' => 'required|array',
            'schedules.*.*.session_type' => 'required|string|in:Jam Pelajaran,Jam Istirahat',
            'schedules.*.*.start_hour_id' => 'required|exists:hours,id',
            'schedules.*.*.end_hour_id' => 'required|exists:hours,id',
            'schedules.*.*.assignment_id' => 'nullable|exists:teaching_assignments,id',
        ]);

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
            // Hapus semua jadwal lama untuk kelas ini
            ClassSchedule::where('class_id', $class_id)->delete();

            // Buat jadwal baru
            foreach ($schedules as $day => $entries) {
                $dayNumber = $dayMapping[$day] ?? null;

                foreach ($entries as $entry) {
                    $start = (int) $entry['start_hour_id'];
                    $end   = (int) $entry['end_hour_id'];
                    $sessionType = $entry['session_type'];

                    $assignmentId = $sessionType === 'Jam Istirahat'
                        ? null
                        : ($entry['assignment_id'] ?? null);

                    if ($sessionType === 'Jam Pelajaran' && !$assignmentId) {
                        throw new \Exception("Assignment tidak boleh kosong untuk sesi Jam Pelajaran pada hari $day.");
                    }

                    for ($hourId = $start; $hourId <= $end; $hourId++) {
                        ClassSchedule::create([
                            'class_id'      => $class_id,
                            'assignment_id' => $assignmentId,
                            'day_of_week'   => $dayNumber,
                            'hour_id'       => $hourId,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('manage-schedules.index')->with('success', 'Jadwal berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClassSchedule $manageSchedule)
{
    try {
        $classId = $manageSchedule->class_id;

        // Hapus semua jadwal yang memiliki class_id sama
        ClassSchedule::where('class_id', $classId)->delete();

        return redirect()->route('manage-schedules.index')
            ->with('success', 'Semua jadwal untuk kelas ini berhasil dihapus.');
    } catch (\Exception $e) {
        return back()->with('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
    }
}

}