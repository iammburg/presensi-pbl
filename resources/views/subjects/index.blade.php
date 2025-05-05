{{-- filepath: c:\laragon\www\presensi-pbl\resources\views\subjects\index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manajemen Data Mata Pelajaran</h1>
    <div class="mb-3 d-flex justify-content-between">
        <a href="{{ route('subjects.create') }}" class="btn btn-primary">Tambah Mata Pelajaran</a>
        {{-- Tombol Plus (+) untuk menambahkan mata pelajaran baru --}}
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
            + Tambah Mata Pelajaran Baru
        </button>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No.</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $key => $subject)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $subject->code }}</td>
                <td>{{ $subject->name }}</td>
                <td>{{ $subject->description ?? 'Opsional' }}</td>
                <td>
                    <a href="{{ route('subjects.edit', $subject->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $subjects->links() }} {{-- Pagination --}}
</div>

{{-- Modal untuk Tambah Mata Pelajaran Baru --}}
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('subjects.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addSubjectModalLabel">Tambah Mata Pelajaran Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="code">Kode Mata Pelajaran</label>
                        <input type="text" name="code" id="code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Nama Mata Pelajaran</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection