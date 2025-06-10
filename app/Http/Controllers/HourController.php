<?php

namespace App\Http\Controllers;

use App\Models\Hour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class HourController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read_hours')->only('index');
        $this->middleware('permission:create_hours')->only('create', 'store');
        $this->middleware('permission:update_hours')->only('edit', 'update');
        $this->middleware('permission:delete_hours')->only('destroy');
    }

    public function index()
    {
        if (request()->ajax()) {
            $hours = Hour::select('hours.*');

            return DataTables::of($hours)
                ->addIndexColumn()
                ->addColumn('action', function ($hour) {
                    $actions = '';
                    if (Auth::check()) {
                        $actions .= "<a href='" . route('manage-hours.edit', $hour->id) . "' class='btn btn-sm btn-info mr-1'><i class='fas fa-edit'></i></a>";
                        $actions .= "<button class='btn btn-sm btn-danger' onclick='deleteHour(\"{$hour->id}\")'><i class='fas fa-trash'></i></button>";
                    }
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('manage-hours.index');
    }

    public function create()
    {
        return view('manage-hours.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'session_type' => 'required|in:Jam pelajaran,Jam istirahat',
            'slot_number' => 'required|integer|min:1|unique:hours,slot_number',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ], [
            'slot_number.unique' => 'Nomor jam tersebut sudah digunakan. Silakan pilih nomor lain.',
        ]);

        Hour::create([
            'session_type' => $request->session_type,
            'slot_number' => $request->slot_number,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('manage-hours.index')->with('success', 'Jam berhasil ditambahkan.');
    }

    public function edit($hour)
    {
        $hour = Hour::findOrFail($hour);
        if (!$hour) {
            return redirect()->route('manage-hours.index')->with('error', 'Jam tidak ditemukan.');
        }
        return view('manage-hours.edit', compact('hour'));
    }

    public function update(Request $request, $hour)
    {
        $hour = Hour::findOrFail($hour);
        if (!$hour) {
            return redirect()->route('manage-hours.index')->with('error', 'Jam tidak ditemukan.');
        }
        $request->validate([
            'session_type' => 'required|in:Jam pelajaran,Jam istirahat',
            'slot_number' => 'required|integer|min:1|unique:hours,slot_number,' . $hour->id,
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ], [
            'slot_number.unique' => 'Nomor jam tersebut sudah digunakan. Silakan masukkan nomor lain.',
        ]);

        $hour->update([
            'session_type' => $request->session_type,
            'slot_number' => $request->slot_number,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('manage-hours.index')->with('success', 'Jam berhasil diperbarui.');
    }

    public function destroy($hour)
    {
        $hour = Hour::findOrFail($hour);
        $hour->delete();
        if (request()->ajax()) {
            return response()->json(['message' => 'Jam berhasil dihapus.']);
        }
        return redirect()->route('manage-hours.index')->with('success', 'Jam berhasil dihapus.');
    }

    // Jadwal pelajaran guru (fitur jadwal mengajar saya)
    public function teacherSchedule(Request $request)
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        if (!$teacher) {
            abort(403, 'Akses hanya untuk guru.');
        }

        $academicYears = \App\Models\AcademicYear::orderByDesc('start_year')->get();
        $activeAcademicYear = $request->academic_year_id
            ? \App\Models\AcademicYear::find($request->academic_year_id)
            : \App\Models\AcademicYear::where('is_active', 1)->first();
        $activeAcademicYearId = $activeAcademicYear ? $activeAcademicYear->id : null;

        $teachingAssignments = \App\Models\TeachingAssignment::with(['subject', 'schoolClass'])
            ->where('teacher_id', $teacher->nip)
            ->where('academic_year_id', $activeAcademicYearId)
            ->get();

        $schedules = \App\Models\ClassSchedule::with(['assignment.subject', 'schoolClass', 'hour'])
            ->whereIn('assignment_id', $teachingAssignments->pluck('id'))
            ->orderBy('day_of_week')
            ->orderBy('hour_id')
            ->get();

        $days = [1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat'];
        $grouped = [];
        foreach ($days as $dayNum => $dayName) {
            // Filter schedules for this day
            $daySchedules = $schedules->where('day_of_week', $dayNum)->sortBy(function($item) {
                return $item->hour ? $item->hour->slot_number : 0;
            });
            // Group by class and subject
            $temp = [];
            foreach ($daySchedules as $item) {
                $class = $item->schoolClass->name . ($item->schoolClass->parallel_name ? ' - ' . $item->schoolClass->parallel_name : '');
                $subject = $item->assignment && $item->assignment->subject ? $item->assignment->subject->name : '-';
                $slot = $item->hour ? $item->hour->slot_number : null;
                $start_time = $item->hour ? $item->hour->start_time : null;
                $end_time = $item->hour ? $item->hour->end_time : null;
                $key = $class . '|' . $subject;
                $temp[$key][] = [
                    'slot' => $slot,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'item' => $item,
                ];
            }
            // Merge consecutive slots for each class/subject
            foreach ($temp as $key => $entries) {
                usort($entries, function($a, $b) { return $a['slot'] <=> $b['slot']; });
                $merged = [];
                $i = 0;
                while ($i < count($entries)) {
                    $start = $entries[$i]['slot'];
                    $start_time = $entries[$i]['start_time'];
                    $end = $entries[$i]['slot'];
                    $end_time = $entries[$i]['end_time'];
                    $subject = $entries[$i]['item']->assignment && $entries[$i]['item']->assignment->subject ? $entries[$i]['item']->assignment->subject->name : '-';
                    $class = $entries[$i]['item']->schoolClass->name . ($entries[$i]['item']->schoolClass->parallel_name ? ' - ' . $entries[$i]['item']->schoolClass->parallel_name : '');
                    $j = $i;
                    while (
                        $j + 1 < count($entries)
                        && $entries[$j+1]['slot'] == $entries[$j]['slot'] + 1
                        && $subject == ($entries[$j+1]['item']->assignment && $entries[$j+1]['item']->assignment->subject ? $entries[$j+1]['item']->assignment->subject->name : '-')
                        && $class == ($entries[$j+1]['item']->schoolClass->name . ($entries[$j+1]['item']->schoolClass->parallel_name ? ' - ' . $entries[$j+1]['item']->schoolClass->parallel_name : ''))
                    ) {
                        $end = $entries[$j+1]['slot'];
                        $end_time = $entries[$j+1]['end_time'];
                        $j++;
                    }
                    $merged[] = [
                        'day' => $dayName,
                        'time' => ($start == $end)
                            ? ($start_time . ' - ' . $end_time)
                            : ('Jam ' . $start . '-' . $end . ' (' . $start_time . ' - ' . $end_time . ')'),
                        'subject' => $subject,
                        'class' => $class,
                    ];
                    $i = $j + 1;
                }
                foreach ($merged as $m) {
                    $grouped[] = $m;
                }
            }
        }

        $weeklySchedules = $grouped;

        $calendarEvents = [];
        foreach ($schedules as $schedule) {
            $dayNum = $schedule->day_of_week;
            $start = $schedule->hour ? $schedule->hour->start_time : '07:00';
            $end = $schedule->hour ? $schedule->hour->end_time : '08:00';
            $date = now()->startOfWeek()->addDays($dayNum-1)->toDateString();
            $calendarEvents[] = [
                'title' => ($schedule->assignment->subject->name ?? '-') . ' (' . ($schedule->schoolClass->name ?? '-') . ')',
                'start' => $date . 'T' . $start,
                'end' => $date . 'T' . $end,
            ];
        }

        $teacherName = $teacher->name;
        return view('hours.index', compact(
            'academicYears',
            'activeAcademicYearId',
            'weeklySchedules',
            'calendarEvents',
            'teacherName'
        ));
    }
}