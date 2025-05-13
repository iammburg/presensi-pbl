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
            // Ambil data beserta relasi academicYear
            $classes = SchoolClass::with('academicYear')->select('classes.*');

            return DataTables::of($classes)
                ->addIndexColumn()
                ->addColumn('academic_year', function ($class) {
                    if ($class->academicYear) {
                        $semesterText = $class->academicYear->semester == 0 ? 'Ganjil' : 'Genap';
                        return $class->academicYear->start_year . ' - ' . $class->academicYear->end_year . ' ' . $semesterText . '';
                    }
                    return '-';
                })
                
                ->addColumn('status', function ($class) {
                    return $class->is_active
                        ? 'Aktif'
                        : 'Tidak Aktif';
                })
                ->addColumn('action', function ($class) {
                    $editUrl = route('manage-classes.edit', $class->id);
                    return <<<HTML
                        <div class="dropdown">
                            <button class="btn btn-outline-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cog"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{$editUrl}"><i class="fas fa-edit me-1"></i>Edit</a>
                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteClass({$class->id})"><i class="fas fa-trash-alt me-1"></i>Delete</a>
                            </div>
                        </div>
                    HTML;
                })
                ->rawColumns(['status', 'action']) // Render HTML pada kolom status & action
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