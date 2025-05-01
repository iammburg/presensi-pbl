<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::with('teacher')->paginate(10); // menampilkan data dengan pagination
        return view('manage-subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $validatedData = $request->validate([
            'code' => 'required|string|max:10|unique:subjects,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        // Tambahkan default untuk is_active jika belum diset di database
        $validatedData['is_active'] = 1;

        // Simpan ke database
        Subject::create($validatedData);

        return redirect()->route('subject.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        //
    }

    public function create()
    {
        return view('manage-subjects.create');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        return view('manage-subjects.edit', compact('subject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $subject->update($request->all());

        return redirect()->route('subject.index')->with('success', 'Mata pelajaran berhasil diubah.'); // Atau ke halaman lain setelah update
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        // Menghapus subject
        $subject->delete();

        // Redirect ke halaman index atau halaman lain setelah penghapusan
        return redirect()->route('subject.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }

}

