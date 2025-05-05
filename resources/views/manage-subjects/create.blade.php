@extends('layouts.app')

@section('content')
<div class="content">
    <div class="container-fluid">

        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0">Tambah Mata Pelajaran</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right"></ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12"> <!-- Full width -->
                <div class="card card-primary card-outline position-relative">
                    <!-- Tombol kembali di pojok kanan atas -->
                    <a href="{{ route('subject.index') }}" class="btn btn-sm btn-primary position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>

                    <div class="card-header">
                        <h5 class="m-0">Form Tambah Mata Pelajaran</h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('subject.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="code">Kode Mata Pelajaran</label>
                                <input type="text" name="code" id="code" class="form-control" placeholder="Kode Mata Pelajaran" required>
                            </div>

                            <div class="form-group">
                                <label for="name">Nama Mata Pelajaran</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Nama Mata Pelajaran" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea name="description" id="description" rows="3" class="form-control" placeholder="Opsional"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save"></i> SIMPAN
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection