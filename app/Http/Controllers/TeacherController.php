<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use App\Imports\TeachersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Traits\HasPermissions;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\TeacherTemplateExport;

class TeacherController extends Controller
{
    use HasPermissions;

    public function __construct()
    {
        $this->middleware('permission:read_teacher')->only('index');
        $this->middleware('permission:create_teacher')->only('create', 'store');
        $this->middleware('permission:update_teacher')->only('edit', 'update');
        $this->middleware('permission:delete_teacher')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $teachers = Teacher::query()
                ->join('users', 'teachers.user_id', '=', 'users.id')
                ->select('teachers.*', 'users.email');

            return DataTables::of($teachers)
                ->addIndexColumn()
                ->addColumn('role', function ($teacher) {
                    return 'Guru'; // Karena semua teacher pasti rolenya Guru
                })
                ->addColumn('action', function ($teacher) {
                    $actions = '';
                    if (Auth::check()) {
                        $actions .= "<a href='" . route('manage-teachers.edit', $teacher->nip) . "' class='btn btn-sm btn-info mr-1'><i class='fas fa-edit'></i></a>";
                        $actions .= "<button class='btn btn-sm btn-danger' onclick='deleteTeacher(" . $teacher->nip . ")'><i class='fas fa-trash'></i></button>";
                    }
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('teachers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:teachers,nip',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'address' => 'required',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Create user account
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->nip . date('dmY', strtotime($request->birth_date))), // Default password
        ]);
        $user->assignRole('Guru');

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('teacher-photos', 'public');
        }

        // Create teacher record
        Teacher::create([
            'nip' => $request->nip,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'photo' => $photoPath,
            'user_id' => $user->id,
        ]);

        return redirect()->route('manage-teachers.index')
            ->with('success', 'Data guru berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $nip)
    {
        $teacher = Teacher::with('user')->where('nip', $nip)->firstOrFail();
        return view('teachers.show', compact('teacher'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $nip)
    {
        $teacher = Teacher::with('user')->where('nip', $nip)->firstOrFail();
        return view('teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $nip)
    {
        $teacher = Teacher::with('user')->where('nip', $nip)->firstOrFail();

        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $photoPath = $request->file('photo')->store('teacher-photos', 'public');
            $teacher->photo = $photoPath;
        }

        $teacher->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
        ]);

        // Update user name
        $teacher->user->update(['name' => $request->name]);

        return redirect()->route('manage-teachers.index')
            ->with('success', 'Data guru berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $nip)
    {
        $teacher = Teacher::with('user')->where('nip', $nip)->firstOrFail();

        // Delete photo if exists
        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }

        // Delete associated user
        $teacher->user->delete();

        // Delete teacher
        $teacher->delete();

        return response()->json(['message' => 'Data guru berhasil dihapus']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new TeachersImport, $request->file('file'));

            return redirect()->route('manage-teachers.index')
                ->with('success', 'Data guru berhasil diimport');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = "Baris ke-{$failure->row()}: {$failure->errors()[0]}";
            }

            return redirect()->back()
                ->with('error', implode('<br>', $errors));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat import data');
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new TeacherTemplateExport, 'template_import_guru.xlsx');
    }
}
