<?php

namespace App\Http\Controllers;

use App\Models\HomeroomAssignment;
use App\Models\Teacher;
use App\Models\SchoolClass; 
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeroomAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $homeroomAssignments = HomeroomAssignment::with('teacher', 'class', 'academicYear')->get();
        return view('homeroom_assignments.index', compact('homeroomAssignments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = Teacher::all();
        $classes = SchoolClass::all(); 
        $academicYears = AcademicYear::all();
        
        // Debug info
        Log::info('Teachers available:', $teachers->pluck('name', 'id')->toArray());

        return view('homeroom_assignments.create', compact('teachers', 'classes', 'academicYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    try {
        // Validasi normal
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,nip',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);
        
        // Cek apakah guru sudah menjadi wali kelas di kelas manapun pada tahun akademik yang sama
        $existingTeacher = HomeroomAssignment::where('teacher_id', $validated['teacher_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->first();
            
        if ($existingTeacher) {
            return back()->with('error', 'Guru ini sudah ditugaskan sebagai wali kelas di kelas lain pada tahun akademik ini')->withInput();
        }
        
        // Cek apakah kelas sudah memiliki wali kelas pada tahun akademik yang sama
        $existingClassAssignment = HomeroomAssignment::where('class_id', $validated['class_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->first();
            
        if ($existingClassAssignment) {
            return back()->with('error', 'Kelas ini sudah memiliki wali kelas untuk tahun akademik ini')->withInput();
        }
        
        // Create assignment
        $assignment = HomeroomAssignment::create([
            'teacher_id' => $validated['teacher_id'],
            'class_id' => $validated['class_id'],
            'academic_year_id' => $validated['academic_year_id'],
        ]);
        
        return redirect()->route('manage-homeroom-assignments.index')->with('success', 'Berhasil ditambahkan');
    } catch (\Exception $e) {
        Log::error('Error saat menyimpan data: ' . $e->getMessage());
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
    }
}
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            // Debug untuk mengetahui ID yang dikirim
            Log::info('Edit wali kelas dengan ID: ' . $id);
            
            $homeroomAssignment = HomeroomAssignment::findOrFail($id);
            $teachers = Teacher::all();
            $classes = SchoolClass::all();
            $academicYears = AcademicYear::all();
            
            // Debug informasi data
            Log::info('Data wali kelas yang akan diedit:', $homeroomAssignment->toArray());
            
            return view('homeroom_assignments.edit', compact('homeroomAssignment', 'teachers', 'classes', 'academicYears'));
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data edit: ' . $e->getMessage());
            return redirect()->route('manage-homeroom-assignments.index')
                           ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Debug semua data yang dikirim
        Log::info('Data update yang dikirim:', $request->all());
        
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,nip',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);
    
        try {
            $homeroomAssignment = HomeroomAssignment::findOrFail($id);
            
            // Cek apakah guru sudah menjadi wali kelas di kelas lain pada tahun akademik yang sama
            $existingTeacher = HomeroomAssignment::where('teacher_id', $validated['teacher_id'])
                ->where('academic_year_id', $validated['academic_year_id'])
                ->where('id', '!=', $id)
                ->first();
                
            if ($existingTeacher) {
                return back()->with('error', 'Guru ini sudah ditugaskan sebagai wali kelas di kelas lain pada tahun akademik ini')->withInput();
            }
            
            // Cek apakah kelas sudah memiliki wali kelas pada tahun akademik yang sama (selain record ini)
            $existingClassAssignment = HomeroomAssignment::where('class_id', $validated['class_id'])
                ->where('academic_year_id', $validated['academic_year_id'])
                ->where('id', '!=', $id)
                ->first();
                
            if ($existingClassAssignment) {
                return back()->with('error', 'Kelas ini sudah memiliki wali kelas lain untuk tahun akademik ini')->withInput();
            }
            
            $homeroomAssignment->update($validated);
            return redirect()->route('manage-homeroom-assignments.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error saat update data: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $homeroomAssignment = HomeroomAssignment::findOrFail($id);
            $homeroomAssignment->delete();
            return redirect()->route('manage-homeroom-assignments.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error saat menghapus data: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}