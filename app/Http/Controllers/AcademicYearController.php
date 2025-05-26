<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // << Import kelas Rule

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $academicYears = AcademicYear::all();
        return view('academic_years.index', compact('academicYears'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('academic_years.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_year' => 'required|integer|min:2000|max:2100|digits:4',
            'end_year' => 'required|integer|gt:start_year|digits:4',
            'semester' => [
                'required',
                'in:0,1',
                Rule::unique('academic_years')->where(function ($query) use ($request) {
                    return $query->where('start_year', $request->start_year);
                }),
                // Aturan di atas akan memeriksa apakah ada baris di tabel 'academic_years'
                // yang memiliki 'semester' SAMA DENGAN $request->semester DAN
                // 'start_year' SAMA DENGAN $request->start_year.
            ],
            'is_active' => 'required|boolean',
        ], [
            // Pesan error kustom untuk aturan unique pada field semester
            'semester.unique' => 'Kombinasi Tahun Mulai dan Semester ini sudah ada.',
        ]);

        AcademicYear::create([
            'start_year' => $request->start_year,
            'end_year' => $request->end_year,
            'semester' => $request->semester,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('manage-academic-years.index')->with('success', 'Tahun akademik berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicYear $manage_academic_year)
    {
        return view('academic_years.edit', ['academicYear' => $manage_academic_year]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicYear $manage_academic_year)
    {
        $validatedData = $request->validate([
            'start_year' => 'required|integer|min:2000|max:2100|digits:4',
            'end_year' => 'required|integer|gt:start_year|digits:4',
            'semester' => [
                'required',
                'in:0,1',
                Rule::unique('academic_years')->where(function ($query) use ($request) {
                    return $query->where('start_year', $request->start_year);
                })->ignore($manage_academic_year->id), // Abaikan record saat ini ketika memeriksa keunikan
            ],
            'is_active' => 'required|boolean',
        ], [
            // Pesan error kustom
            'semester.unique' => 'Kombinasi Tahun Mulai dan Semester ini sudah ada.',
        ]);

        $manage_academic_year->update($validatedData);

        return redirect()->route('manage-academic-years.index')
            ->with('success', 'Tahun akademik berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $manage_academic_year)
    {
        $manage_academic_year->delete();

        return redirect()->route('manage-academic-years.index')
            ->with('success', 'Tahun akademik berhasil dihapus.');
    }
}