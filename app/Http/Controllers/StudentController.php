<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\SchoolClass;
use App\Imports\StudentsImport;
use App\Exports\StudentTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read_student')->only('index');
        $this->middleware('permission:create_student')->only('create', 'store', 'import');
        $this->middleware('permission:update_student')->only('edit', 'update');
        $this->middleware('permission:delete_student')->only('destroy');
    }

    public function index()
    {
        if (request()->ajax()) {
            $students = Student::with(['user'])->select('students.*');

            return DataTables::of($students)
                ->addIndexColumn()
                // ->addColumn('class', function ($student) {
                //     return $student->class->name ?? '-';
                // })
                ->addColumn('action', function ($student) {
                    $actions = '';
                    if (Auth::check()) {
                        $actions .= "<a href='" . route('manage-students.edit', $student->nisn) . "' class='btn btn-sm btn-info mr-1'><i class='fas fa-edit'></i></a>";
                        $actions .= "<button class='btn btn-sm btn-danger' onclick='deleteStudent(\"" . $student->nisn . "\")'><i class='fas fa-trash'></i></button>";
                    }
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('students.index');
    }

    public function create()
    {
        $classes = SchoolClass::all();
        return view('students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|unique:students,nisn',
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'parent_email' => 'required|email|unique:users,email',
            'enter_year' => 'required|digits:4',
            'class_id' => 'nullable|exists:classes,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->parent_email,
                'password' => Hash::make($request->nisn . date('dmY', strtotime($request->birth_date))),
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('Siswa');
            }

            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('student-photos', 'public');
            }

            Student::create([
                'nisn' => $request->nisn,
                'name' => $request->name,
                'address' => $request->address,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'phone' => $request->phone,
                'parent_name' => $request->parent_name,
                'parent_phone' => $request->parent_phone,
                'parent_email' => $request->parent_email,
                'enter_year' => $request->enter_year,
                'photo' => $photoPath,
                'user_id' => $user->id,
                'class_id' => $request->class_id,
                'is_active' => true,
            ]);

            return redirect()->route('manage-students.index')
                ->with('success', 'Data siswa berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan data siswa:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data siswa.');
        }
    }

    public function edit($nisn)
    {
        $student = Student::where('nisn', $nisn)->firstOrFail();
        $classes = SchoolClass::all();
        return view('students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, $nisn)
    {
        $student = Student::where('nisn', $nisn)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'parent_email' => 'required|email|unique:users,email,' . $student->user_id,
            'enter_year' => 'required|digits:4',
            'class_id' => 'nullable|exists:classes,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($student->photo) {
                    Storage::disk('public')->delete($student->photo);
                }

                // Store new photo
                $photoPath = $request->file('photo')->store('student-photos', 'public');
                $student->photo = $photoPath;
            }

            $student->update([
                'name' => $request->name,
                'address' => $request->address,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'phone' => $request->phone,
                'parent_name' => $request->parent_name,
                'parent_phone' => $request->parent_phone,
                'parent_email' => $request->parent_email,
                'enter_year' => $request->enter_year,
                'class_id' => $request->class_id ?? $student->class_id,
            ]);

            $student->user->update([
                'name' => $request->name,
                'email' => $request->parent_email,
            ]);

            return redirect()->route('manage-students.index')
                ->with('success', 'Data siswa berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui data siswa:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data siswa.');
        }
    }

    public function destroy($nisn)
    {
        $student = Student::where('nisn', $nisn)->firstOrFail();

        try {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }

            if ($student->user) {
                $student->user->delete();
            }

            $student->delete();

            return response()->json(['message' => 'Data siswa berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Error saat menghapus data siswa:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus data siswa.'], 500);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));
            return redirect()->route('manage-students.index')
                ->with('success', 'Data siswa berhasil diimport.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $errors = collect($e->failures())->map(function ($failure) {
                return "Baris {$failure->row()}: {$failure->errors()[0]}";
            })->implode('<br>');

            return redirect()->back()->with('error', $errors);
        } catch (\Exception $e) {
            Log::error('Error saat import data siswa:', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat import data siswa.');
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new StudentTemplateExport, 'template_import_siswa.xlsx');
    }

    public function detail($nisn)
    {
        $student = Student::where('nisn', $nisn)->firstOrFail();
        return response()->json([
            'nisn' => $student->nisn,
            'name' => $student->name,
            'address' => $student->address,
            'phone' => $student->phone,
            'gender' => $student->gender,
            'enter_year' => $student->enter_year,
            'parent_name' => $student->parent_name,
            'parent_phone' => $student->parent_phone,
            'parent_email' => $student->parent_email,
            'birth_date' => $student->birth_date,
            'photo_url' => $student->photo ? asset('storage/' . $student->photo) : null,
        ]);
    }
}
