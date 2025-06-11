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
            ->orderByRaw("FIELD(validation_status, 'pending', 'approved', 'rejected')")
            ->orderByDesc('violation_date')
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
            $user = Auth::user();
            // Ambil NIP guru dari relasi teacher (BUKAN teacherProfile)
            if ($user->teacher && $user->teacher instanceof \App\Models\Teacher) {
                $validatorTeacherId = $user->teacher->nip;
            }
        }

        $updateData = [
            'validation_status' => $request->validation_status,
            'validation_notes' => $request->validation_notes,
            'validator_id' => $validatorTeacherId, // Simpan ID Guru sebagai validator
            'validated_at' => now(),
        ];

        // Logika untuk memperbarui 'status' utama berdasarkan 'validation_status'
        if ($request->validation_status === 'approved' || $request->validation_status === 'rejected') {
            $updateData['status'] = 'completed'; // Ubah ke 'completed' baik disetujui maupun ditolak
        }

        $violation->update($updateData);

        return redirect()->route('violation-validations.index') // Pastikan nama route ini benar
            ->with('success', 'Pelanggaran berhasil divalidasi');
    }
}
