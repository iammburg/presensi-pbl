@extends('layouts.app')

@section('title', 'Tambah Data Kelas')

@section('content')
<div class="container-fluid px-4">
    <h3 class="fw-bold mb-4">Tambah Data Kelas</h3>

    <div class="card shadow-sm rounded">
        <div class="card-header">
            <h5 class="mb-0">Form Tambah Data Kelas</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('manage-classes.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Kelas</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="parallel_name" class="form-label">Parallel</label>
                    <input type="text" class="form-control" id="parallel_name" name="parallel_name" value="{{ old('parallel_name') }}" required>
                    @error('parallel_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="academic_year_id" class="form-label">Tahun Akademik</label>
                    <select class="form-control" id="academic_year_id" name="academic_year_id" required>
                        <option value="">-- Pilih Tahun Akademik --</option>
                        @foreach ($academicYears as $year)
                            <option value="{{ $year->id }}">
                                {{ $year->year_label }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="is_active" class="form-label">Status</label>
                    <select class="form-control" id="is_active" name="is_active" required>
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    @error('is_active')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>



                <button type="submit" class="btn btn-primary">Tambah</button>
                <a href="{{ route('manage-classes.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
