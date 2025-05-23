<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViolationValidationController extends Controller
{
    public function index()
    {
        $violations = Violation::with(['student', 'violationPoint', 'teacher'])
            ->where('validation_status', 'pending')
            ->latest()
            ->paginate(10);

        return view('violations.validation.index', compact('violations'));
    }

    public function show($id)
    {
        $violation = Violation::with(['student', 'violationPoint', 'teacher'])->findOrFail($id);
        return view('violations.validation.show', compact('violation'));
    }

    public function validateViolation(Request $request, Violation $violation)
    {
        $request->validate([
            'validation_status' => 'required|in:approved,rejected',
            'validation_notes' => 'required_if:validation_status,rejected|nullable|string'
        ]);

        $violation->update([
            'validation_status' => $request->validation_status,
            'validation_notes' => $request->validation_notes,
            'validator_id' => optional(Auth::user()->teacher)->nip,
            'validated_at' => now(),
        ]);

        return redirect()->route('violation-validations.index')
            ->with('success', 'Pelanggaran berhasil divalidasi');
    }
}
