<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\ClassSchedule;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create_attendance')->only(['create', 'store']);
        $this->middleware('permission:read_attendance')->only(['index', 'show']);
        $this->middleware('permission:update_attendance')->only(['edit', 'update']);
        $this->middleware('permission:delete_attendance')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->hasRole('Guru')) {
            $classSchedules = ClassSchedule::with(['assignment'])
                ->whereHas('assignment', function ($query) {
                    $query->where('teacher_id', Auth::user()->teacher->nip);
                })
                ->get();
            return view('attendances.index', compact('classSchedules'));
        } else {
            return view('attendances.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
