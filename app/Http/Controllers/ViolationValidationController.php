<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use App\Models\Teacher; // Pastikan model Teacher diimport jika belum
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViolationValidationController extends Controller
{
    public function index()
    {
        $violations = Violation::with(['student', 'violationPoint', 'teacher']) // teacher di sini adalah pelapor
            ->where('validation_status', 'pending')
            ->latest('violation_date') // Urutkan berdasarkan tanggal pelanggaran juga bisa jadi opsi
            ->paginate(10);

        return view('violations.validation.index', compact('violations')); // Pastikan view ini ada
    }

    public function show($id) // Sebaiknya gunakan Route Model Binding
    {
        // $violation = Violation::with(['student', 'violationPoint', 'teacher'])->findOrFail($id);
        // Dengan Route Model Binding:
        $violation = Violation::with(['student', 'violationPoint', 'teacher', 'validator'])->findOrFail($id);
        return view('violations.validation.show', compact('violation')); // Pastikan view ini ada
    }

    // Ganti parameter $id dengan Route Model Binding jika memungkinkan di route Anda
    public function validateViolation(Request $request, Violation $violation)
    {
        $request->validate([
            'validation_status' => 'required|in:approved,rejected',
            'validation_notes' => 'required_if:validation_status,rejected|nullable|string|max:1000' // Max length
        ]);

        $validatorTeacherId = null;
        if (Auth::check()) {
            $user = Auth::user(); // Ini adalah model User Laravel
            // Anda perlu cara untuk mendapatkan instance Teacher yang terkait dengan User yang login
            // Contoh: jika User punya relasi one-to-one ke Teacher bernama 'teacherProfile'
            if ($user->teacherProfile && $user->teacherProfile instanceof Teacher) {
                $validatorTeacherId = $user->teacherProfile->id; // Ambil ID dari model Teacher
            }
            // Jika User yang login adalah Teacher itu sendiri (misal, tabel users adalah tabel teachers)
            // elseif ($user instanceof Teacher) {
            //     $validatorTeacherId = $user->id;
            // }
            // Sesuaikan logika ini dengan bagaimana Anda mengelola User dan Teacher
        }

        $updateData = [
            'validation_status' => $request->validation_status,
            'validation_notes' => $request->validation_notes,
            'validator_id' => $validatorTeacherId, // Simpan ID Guru sebagai validator
            'validated_at' => now(),
        ];

        // Logika untuk memperbarui 'status' utama berdasarkan 'validation_status'
        if ($request->validation_status === 'approved') {
            $updateData['status'] = 'processed'; // Atau 'pending' jika masih ada langkah lain
        } elseif ($request->validation_status === 'rejected') {
            // $updateData['status'] = 'pending'; // Atau status lain yang sesuai
        }

        $violation->update($updateData);

        return redirect()->route('violation-validations.index') // Pastikan nama route ini benar
            ->with('success', 'Pelanggaran berhasil divalidasi');
    }
}
