@extends('layouts.app')

@section('title')
    Edit Keputusan Validasi
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-uppercase">Edit Keputusan Validasi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('violation-validations.index') }}">Validasi Pelanggaran</a></li>
                    <li class="breadcrumb-item active">Edit Validasi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Form Edit Keputusan Validasi</h3>
                    </div>
                    <form action="{{ route('violation-validations.updateValidation', $violation->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="validation_status">Status Validasi <span class="text-danger">*</span></label>
                                <select name="validation_status" id="validation_status" class="form-control" required>
                                    <option value="approved" {{ $violation->validation_status == 'approved' ? 'selected' : '' }}>Setujui Laporan</option>
                                    <option value="rejected" {{ $violation->validation_status == 'rejected' ? 'selected' : '' }}>Tolak Laporan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="validation_notes">Catatan Validasi (Opsional)</label>
                                <textarea name="validation_notes" id="validation_notes" class="form-control" rows="3">{{ old('validation_notes', $violation->validation_notes) }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="{{ route('violation-validations.show', $violation->id) }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
