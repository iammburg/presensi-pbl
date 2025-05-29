<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use App\Models\Student;
use App\Models\ViolationPoint;
use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\HomeroomAssignment;
use App\Models\StudentClassAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ViolationController extends Controller
{
    public function __construct()
    {
        $this->middleware('homeroom.teacher')->except(['index', 'show']);
    }

    public function index()
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        if ($teacher) {
            // Hanya tampilkan pelanggaran yang dilaporkan oleh guru yang login
            $violations = Violation::with(['student', 'violationPoint', 'academicYear', 'teacher'])
                ->where('reported_by', $teacher->nip)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Jika bukan guru, tidak tampilkan apapun (atau bisa diubah sesuai kebutuhan)
            $violations = collect([]);
        }
        return view('violations.index', compact('violations'));
    }

    public function create()
    {
        $teacher = Auth::user()->teacher;

        // Ambil kelas yang diampu oleh guru sebagai wali kelas
        $homeroomClass = HomeroomAssignment::where('teacher_id', $teacher->nip)
            ->whereHas('academicYear', function($query) {
                $query->where('is_active', true);
            })
            ->first();

        if (!$homeroomClass) {
            return redirect()->route('home')->with('error', 'Anda tidak memiliki kelas yang diampu sebagai wali kelas.');
        }

        // Ambil siswa dari kelas tersebut
        $students = Student::whereHas('classAssignments', function($query) use ($homeroomClass) {
            $query->where('class_id', $homeroomClass->class_id)
                  ->where('academic_year_id', $homeroomClass->academic_year_id);
        })->get();

        $violationPoints = ViolationPoint::all();
        $academicYears = AcademicYear::where('is_active', true)->get();

        return view('violations.create', compact('students', 'violationPoints', 'academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,nisn',
            'violation_points_id' => 'required|exists:violation_points,id',
            'violation_date' => 'required|date',
            'academic_year_id' => 'required|exists:academic_years,id',
            'description' => 'required|string',
            'penalty' => 'nullable|string',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'required|in:pending,processed,completed,rejected'
        ]);

        DB::beginTransaction();
        try {
            $teacher = Auth::user()->teacher;
            if (!$teacher) {
                return back()->with('error', 'Hanya guru yang dapat melaporkan pelanggaran.');
            }

            // Verifikasi bahwa siswa tersebut adalah siswa dari kelas yang diampu guru
            $homeroomClass = HomeroomAssignment::where('teacher_id', $teacher->nip)
                ->whereHas('academicYear', function($query) {
                    $query->where('is_active', true);
                })
                ->first();

            if (!$homeroomClass) {
                return back()->with('error', 'Anda tidak memiliki kelas yang diampu sebagai wali kelas.');
            }

            $isStudentInClass = StudentClassAssignment::where('student_id', $request->student_id)
                ->where('class_id', $homeroomClass->class_id)
                ->where('academic_year_id', $homeroomClass->academic_year_id)
                ->exists();

            if (!$isStudentInClass) {
                return back()->with('error', 'Siswa tersebut bukan dari kelas yang Anda ampu.');
            }

            $violation = new Violation([
                'student_id' => $request->student_id,
                'violation_points_id' => $request->violation_points_id,
                'violation_date' => $request->violation_date,
                'academic_year_id' => $request->academic_year_id,
                'description' => $request->description,
                'penalty' => $request->penalty,
                'status' => $request->status,
                'reported_by' => $teacher->nip,
            ]);

            if ($request->hasFile('evidence')) {
                $path = $request->file('evidence')->store('violations/evidence', 'public');
                $violation->evidence = $path;
            }

            $violation->save();

            DB::commit();
            return redirect()->route('violations.index')->with('success', 'Pelanggaran berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Violation $violation)
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        // Cek apakah user adalah pelapor
        if ($teacher && $violation->reported_by !== $teacher->nip) {
            abort(403, 'Anda tidak berhak mengakses detail pelanggaran ini.');
        }
        $violation->load(['student', 'violationPoint', 'academicYear', 'teacher']);
        return view('violations.show', compact('violation'));
    }

    public function edit(Violation $violation)
    {
        $teacher = Auth::user()->teacher;

        // Ambil kelas yang diampu oleh guru sebagai wali kelas
        $homeroomClass = HomeroomAssignment::where('teacher_id', $teacher->nip)
            ->whereHas('academicYear', function($query) {
                $query->where('is_active', true);
            })
            ->first();

        if (!$homeroomClass) {
            return redirect()->route('home')->with('error', 'Anda tidak memiliki kelas yang diampu sebagai wali kelas.');
        }

        // Ambil siswa dari kelas tersebut
        $students = Student::whereHas('classAssignments', function($query) use ($homeroomClass) {
            $query->where('class_id', $homeroomClass->class_id)
                  ->where('academic_year_id', $homeroomClass->academic_year_id);
        })->get();

        $violationPoints = ViolationPoint::all();
        $academicYears = AcademicYear::where('is_active', true)->get();

        return view('violations.edit', compact('violation', 'students', 'violationPoints', 'academicYears'));
    }

    public function update(Request $request, Violation $violation)
    {
        $request->validate([
            'student_id' => 'required|exists:students,nisn',
            'violation_points_id' => 'required|exists:violation_points,id',
            'violation_date' => 'required|date',
            'academic_year_id' => 'required|exists:academic_years,id',
            'description' => 'required|string',
            'penalty' => 'nullable|string',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'required|in:pending,processed,completed,rejected'
        ]);

        DB::beginTransaction();
        try {
            $teacher = Auth::user()->teacher;
            if (!$teacher) {
                return back()->with('error', 'Hanya guru yang dapat mengedit pelanggaran.');
            }

            // Verifikasi bahwa siswa tersebut adalah siswa dari kelas yang diampu guru
            $homeroomClass = HomeroomAssignment::where('teacher_id', $teacher->nip)
                ->whereHas('academicYear', function($query) {
                    $query->where('is_active', true);
                })
                ->first();

            if (!$homeroomClass) {
                return back()->with('error', 'Anda tidak memiliki kelas yang diampu sebagai wali kelas.');
            }

            $isStudentInClass = StudentClassAssignment::where('student_id', $request->student_id)
                ->where('class_id', $homeroomClass->class_id)
                ->where('academic_year_id', $homeroomClass->academic_year_id)
                ->exists();

            if (!$isStudentInClass) {
                return back()->with('error', 'Siswa tersebut bukan dari kelas yang Anda ampu.');
            }

            $violation->update([
                'student_id' => $request->student_id,
                'violation_points_id' => $request->violation_points_id,
                'violation_date' => $request->violation_date,
                'academic_year_id' => $request->academic_year_id,
                'description' => $request->description,
                'penalty' => $request->penalty,
                'status' => $request->status,
            ]);

            if ($request->hasFile('evidence')) {
                // Hapus bukti lama jika ada
                if ($violation->evidence) {
                    Storage::disk('public')->delete($violation->evidence);
                }
                $path = $request->file('evidence')->store('violations/evidence', 'public');
                $violation->evidence = $path;
                $violation->save();
            }

            DB::commit();
            return redirect()->route('violations.index')->with('success', 'Pelanggaran berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Violation $violation)
    {
        DB::beginTransaction();
        try {
            if ($violation->evidence) {
                Storage::disk('public')->delete($violation->evidence);
            }
            $violation->delete();
            DB::commit();
            return redirect()->route('violations.index')->with('success', 'Pelanggaran berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
