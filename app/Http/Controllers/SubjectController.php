<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::with('teacher')->paginate(10); // Ambil data mata pelajaran beserta guru
        return view('subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = Teacher::all(); // Ambil semua data guru
        return view('subjects.create', compact('teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:subjects,code|max:10',
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:teachers,id',
            'description' => 'nullable|string',
        ]);

        Subject::create([
            'code' => $request->code,
            'name' => $request->name,
            'teacher_id' => $request->teacher_id,
            'description' => $request->description,
        ]);

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        return view('subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        $teachers = Teacher::all(); // Ambil semua data guru
        return view('subjects.edit', compact('subject', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'code' => 'required|unique:subjects,code,' . $subject->id . '|max:10',
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:teachers,id',
            'description' => 'nullable|string',
        ]);

        $subject->update([
            'code' => $request->code,
            'name' => $request->name,
            'teacher_id' => $request->teacher_id,
            'description' => $request->description,
        ]);

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    /**
     * Get schedule names as JSON.
     */
    public function getScheduleNames()
    {
        $scheduleNames = DB::table('subjects')->pluck('name'); // Mengambil nama mata pelajaran dari tabel subjects
        return response()->json($scheduleNames);
    }
}