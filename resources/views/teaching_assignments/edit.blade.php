@extends('layouts.app')

@section('title')
    Ubah Penugasan Guru - Mapel
@endsection

@push('css')
<!-- DataTables (jika diperlukan) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

{{-- <!-- Select2 JS (pastikan jQuery sudah dimuat sebelumnya) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}


<style>
/* Tambahan agar placeholder sama seperti dropdown biasa */
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #797978 !important; /* ganti dengan warna favoritmu */
    color: rgb(183, 55, 55);
}
.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #495057; /* abu-abu bootstrap */
    font-style: normal;
}

.select2-container--default .select2-selection--single .select2-selection__arrow b {
    display: none;
}

/* Tambahkan icon caret seperti native select */
.select2-container--default .select2-selection--single {
    background-image: url("data:image/svg+xml,%3Csvg width='10' height='5' viewBox='0 0 12 5' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%23555' stroke-width='1' fill='none' fill-rule='evenodd'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.2rem center;
    background-size: 12px 14px;
    padding-right: 1rem !important;
}

/* Rounded dropdown list */
.select2-container--default .select2-dropdown {
    border-radius: 12px !important;
    border: .5px solid #abaaa4 !important;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); /* opsional: beri sedikit bayangan */
}

/* Rounded atas dan bawah */
.select2-container--default .select2-results {
    border-radius: 8px !important;
    padding: 4px 0;
}

/* Setiap item di dalam dropdown */
.select2-container--default .select2-results__option {
    padding: 8px 12px;
    border-radius: 0px;
    margin: 0px 0px;
}

/* Hapus tombol clear (x) dari Select2 */
.select2-container--default .select2-selection--single .select2-selection__clear {
    display: none !important;
}

/* Tambahkan jarak antara kotak input dan dropdown list */
.select2-container--default .select2-dropdown {
    margin-top: 4.5px !important;
}
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Ubah Data Pengajaran Guru ke Mapel</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    {{-- Breadcrumb opsional --}}
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Form Ubah Penugasan</h3>
                        <div class="card-tools">
                            <a href="{{ route('manage-teacher-subject-assignments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('manage-teacher-subject-assignments.update', $teacherAssignment->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="academic_year_id">Tahun Akademik</label>
                                <select name="academic_year_id" id="academic_year_id" class="form-control" required>
                                    <option value="">-- Pilih Tahun Akademik --</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ $year->id == $teacherAssignment->academic_year_id ? 'selected' : '' }}>
                                            {{ $year->start_year }} / {{ $year->end_year }} @if($year->semester) (Sem {{ $year->semester }})@endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="class_id">Kelas</label>
                                <select name="class_id" id="class-select" class="form-control select2 @error('class_id') is-invalid @enderror" required data-placeholder="-- Pilih Kelas --">
                                    <option></option> {{-- Placeholder kosong agar Select2 bisa pakai data-placeholder --}}
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id', $teacherAssignment->class_id) == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}{{ $class->parallel_name ? ' - ' . $class->parallel_name : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="form-group">
                                <label>Mata Pelajaran</label>
                                <select name="subject_id" class="form-control @error('subject_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}"
                                            {{ (old('subject_id', $teacherAssignment->subject_id ?? '') == $subject->id) ? 'selected' : '' }}>
                                            {{ $subject->subject_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="teacher_id">Guru</label>
                                <select id="teacher-select"
                                        name="teacher_id"
                                        class="form-control select2 @error('teacher_id') is-invalid @enderror"
                                        data-placeholder="-- Pilih Guru --"
                                        required>
                                    <option></option> {{-- placeholder Select2 --}}
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->nip }}"
                                            {{ old('teacher_id', $teacherAssignment->teacher_id) == $teacher->nip ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('teacher_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function () {
    $('#teacher-select').select2({
        placeholder: $('#teacher-select').data('placeholder'),
        allowClear: true,
        width: '100%'
    });

    $('#class-select').select2({
        placeholder: $('#class-select').data('placeholder'),
        allowClear: true,
        width: '100%'
    });
});
</script>
@endpush
