{{-- filepath: c:\laragon\www\presensi-pbl\resources\views\subjects\edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Mata Pelajaran</h1>
    <form action="{{ route('subjects.update', $subject->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Metode HTTP PUT untuk update --}}
        
        <div class="form-group">
            <label for="code">Kode Mata Pelajaran</label>
            <input type="text" name="code" id="code" class="form-control" value="{{ $subject->code }}" required>
        </div>

        <div class="form-group">
            <label for="name">Nama Mata Pelajaran</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $subject->name }}" required>
        </div>

        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea name="description" id="description" class="form-control">{{ $subject->description }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
@endsection 