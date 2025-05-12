<?php

namespace App\Http\Controllers;

use App\Models\Hour;
use Illuminate\Http\Request;

class HourController extends Controller
{
    public function index()
    {
        $hours = Hour::all();
        return view('manage-hours.index', compact('hours'));
    }

    public function create()
    {
        return view('manage-hours.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'session_type' => 'required|in:Jam pelajaran,Jam istirahat',
            'slot_number' => 'required|integer|min:1|unique:hours,slot_number',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ], [
            'slot_number.unique' => 'Nomor jam tersebut sudah digunakan. Silakan pilih nomor lain.',
        ]);

        Hour::create([
            'session_type' => $request->session_type,
            'slot_number' => $request->slot_number,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('manage-hours.index')->with('success', 'Jam berhasil ditambahkan.');
    }

    public function edit(Hour $hour)
    {
        return view('manage-hours.edit', compact('hour'));
    }

    public function update(Request $request, Hour $hour)
    {
        $request->validate([
            'session_type' => 'required|in:Jam pelajaran,Jam istirahat',
            'slot_number' => 'required|integer|min:1|unique:hours,slot_number,' . $hour->id,
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ], [
            'slot_number.unique' => 'Nomor jam tersebut sudah digunakan. Silakan masukkan nomor lain.',
        ]);

        $hour->update([
            'session_type' => $request->session_type,
            'slot_number' => $request->slot_number,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('manage-hours.index')->with('success', 'Jam berhasil diperbarui.');
    }

    public function destroy(Hour $hour)
    {
        $hour->delete();
        return redirect()->route('manage-hours.index')->with('success', 'Jam berhasil dihapus.');
    }
}
