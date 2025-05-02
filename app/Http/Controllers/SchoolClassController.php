<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $classes = SchoolClass::with('academicYear')
                ->select('classes.*', 'academic_year_id as academic_year_id'); // Select kolom yang dibutuhkan

            return DataTables::of($classes)
                ->addIndexColumn()
                ->addColumn('academic_year', function ($class) {
                    return $class->academic_year_id;
                })
                ->addColumn('status', function ($class) {
                    return $class->is_active ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>';
                })
                ->addColumn('action', function ($class) {
                    $actions = '';
                    // Tambahkan tombol edit
                    $actions .= "<a href='" . route('manage-classes.edit', $class->id) . "' class='btn btn-sm btn-info mr-1'><i class='fas fa-edit'></i></a>";
                    // Tambahkan tombol delete
                    $actions .= "<button class='btn btn-sm btn-danger' onclick='deleteClass(" . $class->id . ")'><i class='fas fa-trash'></i></button>";
                    return $actions;
                })
                ->rawColumns(['status', 'action']) // Render HTML pada kolom status dan action
                ->make(true);
        }

        return view('manage-classes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Mengambil semua tahun akademik untuk dropdown
        $academicYears = AcademicYear::all();

        // Menampilkan form tambah kelas
        return view('manage-classes.create', compact('academicYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parallel_name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'is_active' => 'required|in:0,1',
        ]);

        // Menyimpan data kelas
        SchoolClass::create([
            'name' => $request->name,
            'parallel_name' => $request->parallel_name,
            'academic_year_id' => $request->academic_year_id,
            'is_active' => $request->is_active,   // Sesuaikan dengan kebutuhan
        ]);

        return redirect()->route('manage-classes.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SchoolClass $schoolClass)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $class = SchoolClass::findOrFail($id);
        $academicYears = AcademicYear::all();
        return view('manage-classes.edit', compact('class', 'academicYears'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parallel_name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'is_active' => 'required|boolean',
        ]);

        $class = SchoolClass::findOrFail($id);

        $class->update([
            'name' => $request->name,
            'parallel_name' => $request->parallel_name,
            'academic_year_id' => $request->academic_year_id,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('manage-classes.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $class = SchoolClass::findOrFail($id);

        // Jika ingin juga mengecek relasi atau kondisi lain, bisa ditambahkan di sini

        $class->delete();

        return redirect()->route('manage-classes.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}