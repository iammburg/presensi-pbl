@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="m-0">Tambah Mata Pelajaran</h5>
                </div>
                <form action="{{ route('subject.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="code">Kode Mata Pelajaran</label>
                            <input type="text" name="code" class="form-control" placeholder="Contoh: MAT12" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Nama Mata Pelajaran</label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Matematika Lanjutan" required>
                        </div>
                        <!-- Elemen Guru Pengampu telah dihapus sepenuhnya -->
                        <div class="form-group">
                            <label for="description">Deskripsi (Opsional)</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Contoh: Mata pelajaran wajib untuk jurusan IPA."></textarea>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <a href="{{ route('subject.index') }}" class="btn btn-secondary mr-2">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection