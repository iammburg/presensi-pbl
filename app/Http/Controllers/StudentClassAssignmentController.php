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
        // Ambil semua siswa, plus eager load assignment terakhir beserta schoolClass
        $students = Student::with('currentAssignment');

        return DataTables::of($students)
            ->addIndexColumn()
            // kolom‐kolom dasar
            ->addColumn('nisn',       fn($s) => $s->nisn)
            ->addColumn('nis',        fn($s) => $s->nis)
            ->addColumn('name',       fn($s) => $s->name)
            ->addColumn('gender',     fn($s) => $s->gender === 'L' ? 'Laki-laki' : 'Perempuan')
            ->addColumn('enter_year', fn($s) => $s->enter_year)
            // kolom kelas: cek currentAssignment
            ->addColumn('class_name', function($s) {
                if (! $s->currentAssignment || ! $s->currentAssignment->schoolClass) {
                    return '-';
                }
                $c = $s->currentAssignment->schoolClass;
                return "{$c->name} {$c->parallel_name}";
            })
            ->make(true);
    }

    return view('student_class_assignments.create', [
        'classes'       => SchoolClass::all(),
        'academicYears' => AcademicYear::all(),
    ]);
}
// Versi BARU (Sudah diperbaiki)
public function createForClass(Request $request, $class_id)
{
    $selectedClass = SchoolClass::with('academicYear')->find($class_id);

    if (!$selectedClass) {
        return redirect()->route('manage-classes.index')->with('error', 'Kelas tidak ditemukan.');
    }

    if ($request->ajax()) {
        // PENTING: Kita mulai dengan Query Builder, bukan collection, agar bisa di-join
        $students = Student::query()
            ->leftJoin('student_class_assignments', function ($join) {
                // Join ke assignment TERBARU dari setiap siswa
                $join->on('students.nisn', '=', 'student_class_assignments.student_id')
                    ->whereRaw('student_class_assignments.id = (select max(id) from student_class_assignments as sca where sca.student_id = students.nisn)');
            })
            ->leftJoin('classes', 'student_class_assignments.class_id', '=', 'classes.id')
            ->select('students.*', 'classes.name as class_name_text', 'classes.parallel_name as class_parallel_name'); // Pilih kolom yang dibutuhkan

        return DataTables::of($students)
            ->addIndexColumn()
            // Kolom NISN
            ->addColumn('nisn', fn($s) => $s->nisn)
            ->orderColumn('nisn', fn($query, $direction) => $query->orderBy('students.nisn', $direction))
            // Kolom NIS
            ->addColumn('nis', fn($s) => $s->nis)
            ->orderColumn('nis', fn($query, $direction) => $query->orderBy('students.nis', $direction))
            // Kolom Nama
            ->addColumn('name', fn($s) => $s->name)
            ->orderColumn('name', fn($query, $direction) => $query->orderBy('students.name', $direction))
            // Kolom Jenis Kelamin
            ->addColumn('gender', fn($s) => $s->gender === 'L' ? 'Laki-laki' : 'Perempuan')
            ->orderColumn('gender', fn($query, $direction) => $query->orderBy('students.gender', $direction))
            // Kolom Tahun Masuk
            ->addColumn('enter_year', fn($s) => $s->enter_year)
            ->orderColumn('enter_year', fn($query, $direction) => $query->orderBy('students.enter_year', $direction))
            // Kolom Kelas Saat Ini (Logika sorting kustom karena dari JOIN)
            ->addColumn('class_name', function($s) {
                if ($s->class_name_text) {
                    return "{$s->class_name_text} {$s->class_parallel_name}";
                }
                return '-';
            })
            ->orderColumn('class_name', function ($query, $direction) {
                // Urutkan berdasarkan gabungan nama kelas dan paralel dari tabel `classes`
                $query->orderBy('classes.name', $direction)->orderBy('classes.parallel_name', $direction);
            })
            // Pastikan kolom 'Pilih' tidak bisa diurutkan
            ->rawColumns(['class_name']) // Jika Anda menggunakan HTML di dalam kolom class_name, tambahkan ini
            ->make(true);
    }

    return view('student_class_assignments.create', [
        'academicYears'   => AcademicYear::orderBy('start_year', 'desc')->orderBy('semester', 'desc')->get(),
        'selectedClassId' => $class_id,
        'selectedClass'   => $selectedClass,
    ]);
}


    public function store(Request $request)
{
    // Tambahkan validasi di awal
    $validatedData = $request->validate([
        'nisns'             => 'required|array|min:1',
        'nisns.*'           => 'required|string', // Asumsi NISN adalah string, sesuaikan jika integer
        'class_id'          => 'required|exists:classes,id', // Pastikan class_id valid dan ada di tabel school_classes
        'academic_year_id'  => 'required|exists:academic_years,id', // Pastikan academic_year_id valid
    ]);

    $nisns = $validatedData['nisns'];
    $classId = $validatedData['class_id'];
    $yearId = $validatedData['academic_year_id'];
    $assignedBy = Auth::id();

    // Tambahan: Pastikan user yang melakukan aksi ini memiliki otorisasi (jika diperlukan)
    if (!$assignedBy) {
        return response()->json(['message' => 'Aksi tidak diizinkan. Silakan login terlebih dahulu.'], 403);
    }

    $counter = 0;
    $failedStudentsInfo = []; // Untuk menyimpan info siswa yang gagal diproses

    foreach ($nisns as $nisn) {
        $student = Student::where('nisn', $nisn)->first();

        if ($student) {
            // Logika updateOrCreate Anda sudah baik.
            // Pastikan student_id di tabel student_class_assignments memang menyimpan NISN.
            // Jika student_id seharusnya merujuk ke kolom 'id' di tabel 'students', maka gunakan $student->id.
            StudentClassAssignment::updateOrCreate(
                [
                    'student_id'       => $nisn, // Atau $student->id jika kolom ini adalah foreign key ke students.id
                    'academic_year_id' => $yearId,
                ],
                [
                    'class_id'    => $classId,
                    'assigned_by' => $assignedBy,
                ]
            );
            $counter++;
        } else {
            // Kumpulkan info siswa yang tidak ditemukan
            $failedStudentsInfo[] = $nisn;
        }
    }

    if (count($failedStudentsInfo) > 0) {
        $errorMessage = $counter . ' siswa berhasil dipindahkan. Namun, ' . count($failedStudentsInfo) .
                        ' siswa dengan NISN berikut tidak ditemukan: ' . implode(', ', $failedStudentsInfo) . '.';
        // Anda bisa memutuskan status code yang sesuai, misal 207 (Multi-Status) jika sebagian berhasil
        return response()->json(['message' => $errorMessage, 'status' => 'partial_success'], 207);
    }

    return response()->json([
        'message' => $counter . ' siswa berhasil dipindahkan.',
        'status' => 'success' // Tambahkan status untuk kemudahan di front-end jika perlu
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
