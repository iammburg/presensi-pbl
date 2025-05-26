<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use App\Models\Student;
use App\Models\ViolationPoint;
use App\Models\AcademicYear;
use App\Models\Teacher; // Pastikan model Teacher diimport
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Untuk file operations

class ViolationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $violations = Violation::with([
            'student',          // Relasi ke Student (via NISN)
            'violationPoint',   // Relasi ke ViolationPoint (pastikan nama relasi benar di model)
            'academicYear',     // Relasi ke AcademicYear
            'teacher',          // Relasi ke Teacher (untuk reported_by via NIP)
            'validator'         // Relasi ke Teacher (untuk validator_id via ID Teacher)
        ])
        ->orderBy('violation_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('violations.index', compact('violations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = Student::orderBy('name')->get();
        $violationPoints = ViolationPoint::orderBy('violation_type')->get();
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();

        return view('violations.create', compact('students', 'violationPoints', 'academicYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|string|max:18|exists:students,nisn',
            'violation_point_id' => 'required|exists:violation_points,id', // Input name: violation_point_id
            'violation_date' => 'required|date',
            'academic_year_id' => 'required|exists:academic_years,id',
            'description' => 'required|string|max:2000',
            'penalty' => 'nullable|string|max:255',
            'evidence' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('violation_evidences', 'public');
        }

        $reportedByNip = null;
        if (Auth::check()) {
            $user = Auth::user(); // Ini model User
            // Asumsi User memiliki relasi ke Teacher, atau atribut NIP. Sesuaikan:
            if ($user->teacherProfile && isset($user->teacherProfile->nip)) { // Contoh jika relasi User ke Teacher adalah 'teacherProfile'
                 $reportedByNip = $user->teacherProfile->nip;
            } elseif (isset($user->nip)) { // Atau jika User model punya atribut NIP
                 $reportedByNip = $user->nip;
            }
        }

        $violation = new Violation();
        $violation->student_id = $validatedData['student_id']; // NISN
        $violation->violation_points_id = $validatedData['violation_point_id']; // Sesuai FK migrasi
        $violation->violation_date = $validatedData['violation_date'];
        $violation->academic_year_id = $validatedData['academic_year_id'];
        $violation->description = $validatedData['description'];
        $violation->penalty = $validatedData['penalty'] ?? null;
        $violation->reported_by = $reportedByNip; // NIP Guru
        $violation->evidence = $evidencePath;

        // Inisialisasi status (berdasarkan klarifikasi Anda nantinya)
        $violation->status = 'pending'; // Status utama
        $violation->validation_status = 'pending'; // Status untuk alur validasi detail

        // validator_id, validation_notes, validated_at akan NULL saat pembuatan awal.
        // Diisi nanti saat proses di method validateReport.

        $violation->save();

        return redirect()->route('violations.index')
                         ->with('success', 'Laporan pelanggaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Violation $violation)
    {
        $violation->load(['student', 'violationPoint', 'academicYear', 'teacher', 'validator']);
        return view('violations.show', compact('violation')); // Anda perlu membuat view 'violations.show'
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Violation $violation)
    {
        $students = Student::orderBy('name')->get();
        $violationPoints = ViolationPoint::orderBy('violation_type')->get();
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        return view('violations.edit', compact('violation', 'students', 'violationPoints', 'academicYears')); // Buat view 'violations.edit'
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Violation $violation)
    {
        $validatedData = $request->validate([
            'student_id' => 'required|string|max:18|exists:students,nisn',
            'violation_point_id' => 'required|exists:violation_points,id',
            'violation_date' => 'required|date',
            'academic_year_id' => 'required|exists:academic_years,id',
            'description' => 'required|string|max:2000',
            'penalty' => 'nullable|string|max:255',
            'evidence' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Anda mungkin ingin bisa mengupdate status dari form edit juga:
            // 'status' => 'sometimes|required|in:pending,processed,completed',
            // 'validation_status' => 'sometimes|required|in:pending,approved,rejected',
        ]);

        if ($request->hasFile('evidence')) {
            if ($violation->evidence && Storage::disk('public')->exists($violation->evidence)) {
                Storage::disk('public')->delete($violation->evidence);
            }
            $evidencePath = $request->file('evidence')->store('violation_evidences', 'public');
            $violation->evidence = $evidencePath;
        }

        $violation->fill($validatedData); // Isi field yang divalidasi (kecuali evidence jika sudah dihandle)
        // Jika evidence tidak masuk $validatedData karena sudah dihandle, set manual
        // if(isset($evidencePath)) $violation->evidence = $evidencePath;

        $violation->save();

        return redirect()->route('violations.index')
                         ->with('success', 'Laporan pelanggaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Violation $violation)
    {
        try {
            if ($violation->evidence && Storage::disk('public')->exists($violation->evidence)) {
                Storage::disk('public')->delete($violation->evidence);
            }
            $violation->delete();
            return redirect()->route('violations.index')
                             ->with('success', 'Laporan pelanggaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('violations.index')
                             ->with('error', 'Gagal menghapus laporan pelanggaran: ' . $e->getMessage());
        }
    }

    /**
     * Validate a violation report.
     */
    public function validateReport(Request $request, Violation $violation)
    {
        $request->validate([
            'validation_status' => 'required|in:approved,rejected', // Sesuai dengan values di form validasi
            'validation_notes' => 'nullable|string|max:1000',
        ]);

        $validatorTeacherId = null;
        if (Auth::check()) {
            $user = Auth::user(); // Ini model User
            // Asumsi User memiliki relasi ke Teacher, misal 'teacherProfile' yang merupakan instance Teacher
            // dan Teacher memiliki primary key 'id'
            if ($user->teacherProfile && $user->teacherProfile instanceof Teacher) {
                $validatorTeacherId = $user->teacherProfile->id;
            }
            // Sesuaikan cara mendapatkan ID Guru yang melakukan validasi.
            // Ini krusial karena relasi 'validator' di model Violation merujuk ke Teacher.
        }

        $violation->validation_status = $request->validation_status;
        $violation->validation_notes = $request->validation_notes;
        $violation->validator_id = $validatorTeacherId; // ID dari tabel teachers
        $violation->validated_at = now();

        // Di sini Anda perlu logika bagaimana 'status' utama dipengaruhi oleh 'validation_status'
        // Contoh sederhana:
        if ($request->validation_status === 'approved') {
            $violation->status = 'processed'; // Atau 'pending' jika masih perlu langkah lain sebelum 'processed'
        } elseif ($request->validation_status === 'rejected') {
            // Mungkin status utama tetap 'pending' atau menjadi 'rejected_validation' jika ada
            // $violation->status = 'pending';
        }

        $violation->save();

        return redirect()->route('violations.index')
                         ->with('success', 'Status validasi laporan pelanggaran berhasil diperbarui.');
    }
}