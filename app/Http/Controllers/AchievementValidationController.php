<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AchievementValidationController extends Controller
{
    public function index()
    {
        $achievements = Achievement::with(['student', 'achievementPoint', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('achievements.validation.index', compact('achievements'));
    }

    public function show($id)
    {
        $achievement = Achievement::with(['student', 'achievementPoint', 'teacher'])->findOrFail($id);
        return view('achievements.validation.show', compact('achievement'));
    }

    public function validateAchievement(Request $request, Achievement $achievement)
    {
        $request->validate([
            'validation_status' => 'required|in:approved,rejected',
            'validation_notes' => 'required_if:validation_status,rejected|nullable|string'
        ]);

        $achievement->update([
            'validation_status' => $request->validation_status,
            'validator_id' => Auth::user()->teacher->nip, // Perbaikan: gunakan nip, bukan id
            'validation_notes' => $request->validation_notes,
            'validated_at' => now()
        ]);

        return redirect()->route('achievement-validations.index')
            ->with('success', 'Prestasi berhasil divalidasi');
    }
}
