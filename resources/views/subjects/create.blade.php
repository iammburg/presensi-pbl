{{-- filepath: c:\laragon\www\presensi-pbl\resources\views\subjects\create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tambah Mata Pelajaran</h1>
    <form action="{{ route('subjects.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="code">Kode Mata Pelajaran</label>
            <input type="text" name="code" id="code" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="name">Nama Mata Pelajaran</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="teacher_id">Pilih Guru Pengampu</label>
            <select name="teacher_id" id="teacher_id" class="form-control" required>
                <option value="">Pilih Guru Pengampu</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label for="new_subject">Tambah Nama Mata Pelajaran Baru</label>
            <input type="text" name="new_subject" id="new_subject" class="form-control" placeholder="Masukkan nama mata pelajaran baru">
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection