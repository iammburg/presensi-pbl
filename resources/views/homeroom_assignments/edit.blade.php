@extends('layouts.app')

@push('css')
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Edit Data Wali Kelas</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title m-0">Form Wali Kelas</h5>
                            <div class="card-tools">
                                <a href="{{ route('manage-homeroom-assignments.index') }}" class="btn btn-tool">
                                    <i class="fas fa-arrow-alt-circle-left"></i>
                                </a>
                            </div>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show m-3">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show m-3">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <form action="{{ route('manage-homeroom-assignments.update', $homeroomAssignment->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                <div class="form-group">
                                    <label for="teacher_id">Guru</label>
                                    <select name="teacher_id" id="teacher_id" class="form-control @error('teacher_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Guru --</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->nip }}" {{ old('teacher_id', $homeroomAssignment->teacher_id) == $teacher->nip ? 'selected' : '' }}>
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('teacher_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="class_id">Kelas</label>
                                    <select name="class_id" id="class_id" class="form-control @error('class_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id', $homeroomAssignment->class_id) == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }} - {{ $class->parallel_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="academic_year_id">Tahun Akademik</label>
                                    <select name="academic_year_id" id="academic_year_id" class="form-control @error('academic_year_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Tahun Akademik --</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('academic_year_id', $homeroomAssignment->academic_year_id) == $year->id ? 'selected' : '' }}>
                                                {{ $year->start_year }}/{{ $year->end_year }} 
                                                {{ $year->semester == 1 ? ' - Ganjil' : ' - Genap' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if(!$homeroomAssignment->academic_year_id)
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle"></i> Perhatian: Tahun akademik belum diatur sebelumnya. Silakan pilih tahun akademik.
                                    </div>
                                @endif
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-block btn-flat text-white" style="background-color: #1777E5">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush
