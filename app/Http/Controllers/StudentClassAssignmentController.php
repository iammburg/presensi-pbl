<?php

namespace App\Http\Controllers;

use App\Models\StudentClassAssignment;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;


class StudentClassAssignmentController extends Controller
{
    public function index(Request $request)
    {
        // Implementasi index jika diperlukan
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            $query = Student::with('classAssignments.schoolClass');

            return DataTables::of($query)
                ->addIndexColumn()                              // No.
                ->addColumn('nisn', fn($r) => $r->nisn)         // NISN
                ->addColumn('nis', fn($r) => $r->nis)           // NIS
                ->addColumn('name', fn($r) => $r->name)         // Nama
                ->addColumn('gender', fn($r) => $r->gender)     // Jenis Kelamin
                ->addColumn('enter_year', fn($r) => $r->enter_year) // Tahun Masuk
                ->addColumn('class_name', function ($r) {       // Kelas
                    $assign = $r->classAssignments->first();
                    return $assign
                        ? $assign->schoolClass->name . ' ' . $assign->schoolClass->parallel_name
                        : '-';
                })
                ->make(true);
        }

        return view('student_class_assignments.create', [
            'classes'       => SchoolClass::all(),
            'academicYears' => AcademicYear::all(),
        ]);
    }

    public function store(Request $request)
    {
        $nisns = $request->input('nisns', []);
        $classId = $request->input('class_id');
        $yearId = $request->input('academic_year_id');

        $counter = 0;
        foreach ($nisns as $nisn) {
            // Cari ID siswa berdasarkan NISN
            $student = Student::where('nisn', $nisn)->first();

            if ($student) {
                StudentClassAssignment::updateOrCreate(
                    [
                        'student_id'       => $nisn,
                        'academic_year_id' => $yearId,
                    ],
                    [
                        'class_id'    => $classId,
                        'assigned_by' => Auth::id(),
                    ]
                );
                $counter++;
            }
        }

        return response()->json([
            'message' => $counter . ' siswa berhasil dipindahkan.'
        ]);
    }

    public function getClassesByYear(Request $request)
    {
        $yearId = $request->input('year_id');
        $classes = SchoolClass::where('academic_year_id', $yearId)->get();
        return response()->json($classes);
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentClassAssignment $studentClassAssignment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentClassAssignment $studentClassAssignment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentClassAssignment $studentClassAssignment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentClassAssignment $studentClassAssignment)
    {
        //
    }
}
