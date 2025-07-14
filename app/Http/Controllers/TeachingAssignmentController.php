<?php

namespace App\Http\Controllers;

use App\Models\TeachingAssignment;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeachingAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create_teacher_subject_assignment', ['only' => ['create', 'store']]);
        $this->middleware('permission:read_teacher_subject_assignment', ['only' => ['index', 'show']]);
        $this->middleware('permission:update_teacher_subject_assignment', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_teacher_subject_assignment', ['only' => ['destroy']]);
    }

    public function index()
    {
        $teachingAssignments = TeachingAssignment::with([
            'academicYear',
            'schoolClass',
            'subject',
            'teacher'
        ])->get();

        return view('teaching_assignments.index', compact('teachingAssignments'));
    }

    public function create()
    {
        $academicYears = AcademicYear::where('is_active', 1)->get();
        $classes       = SchoolClass::all();
        $subjects      = Subject::where('is_active', 1)->get();
        $teachers      = Teacher::all();

        return view('teaching_assignments.create', compact(
            'academicYears',
            'classes',
            'subjects',
            'teachers'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_id'         => 'required|array|min:1',
            'class_id.*'       => 'required|exists:classes,id',
            'subject_id'       => 'required|exists:subjects,id',
            'teacher_id'       => 'required|exists:teachers,nip',
        ]);

        $duplicateClasses = [];

        foreach ($validated['class_id'] as $classId) {
            $exists = TeachingAssignment::where('academic_year_id', $validated['academic_year_id'])
                ->where('class_id', $classId)
                ->where('subject_id', $validated['subject_id'])
                ->where('teacher_id', $validated['teacher_id'])
                ->exists();

            if ($exists) {
                $duplicateClasses[] = $classId;
                continue;
            }

            TeachingAssignment::create([
                'academic_year_id' => $validated['academic_year_id'],
                'class_id'         => $classId,
                'subject_id'       => $validated['subject_id'],
                'teacher_id'       => $validated['teacher_id'],
            ]);
        }

        if (count($duplicateClasses) === count($validated['class_id'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Semua data penugasan sudah ada dan tidak disimpan.');
        }

        return redirect()->route('manage-teacher-subject-assignments.index')
            ->with('success', 'Penugasan berhasil ditambahkan.');
    }


    public function show(TeachingAssignment $teacherAssignment)
    {
        return view('teaching_assignments.show', compact('teacherAssignment'));
    }

    public function edit(TeachingAssignment $teacherAssignment)
    {
        $academicYears = AcademicYear::where('is_active', 1)->get();
        $classes       = SchoolClass::all();
        $subjects      = Subject::where('is_active', 1)->get();
        $teachers      = Teacher::all();

        return view('teaching_assignments.edit', compact(
            'teacherAssignment',
            'academicYears',
            'classes',
            'subjects',
            'teachers'
        ));
    }

    public function update(Request $request, TeachingAssignment $teacherAssignment)
    {
    $validated = $request->validate([
        'academic_year_id' => 'required|exists:academic_years,id',
        'class_id'         => 'required|exists:classes,id',
        'subject_id'       => 'required|exists:subjects,id',
        'teacher_id'       => 'required|exists:teachers,nip',
    ]);

    // Cek duplikat (selain ID yang sedang diedit)
    $exists = TeachingAssignment::where('academic_year_id', $validated['academic_year_id'])
        ->where('class_id', $validated['class_id'])
        ->where('subject_id', $validated['subject_id'])
        ->where('teacher_id', $validated['teacher_id'])
        ->where('id', '!=', $teacherAssignment->id) // exclude current
        ->exists();

    if ($exists) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Data penugasan dengan kombinasi yang sama sudah ada.');
    }

    $teacherAssignment->update($validated);

    return redirect()->route('manage-teacher-subject-assignments.index')
        ->with('success', 'Penugasan berhasil diperbarui.');
    }



    public function destroy(TeachingAssignment $teacherAssignment)
    {
        $teacherAssignment->delete();

        return redirect()->route('manage-teacher-subject-assignments.index')
            ->with('success', 'Penugasan berhasil dihapus.');
    }
}