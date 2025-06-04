@extends('layouts.app')

@section('title')
    Tambah Data Pengajaran Guru - Mapel
@endsection

@push('css')
<!-- Select2 CSS -->
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
                    <h4 class="m-0">Tambah Data Pengajaran Guru ke Mapel</h4>
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
                            <h5 class="card-title m-0">Form Penugasan Guru ke Mapel</h5>
                            <div class="card-tools">
                                <a href="{{ route('manage-teacher-subject-assignments.index') }}" class="btn btn-tool">
                                    <i class="fas fa-arrow-alt-circle-left"></i>
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('manage-teacher-subject-assignments.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        {{ session('error') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label>Mata Pelajaran</label>
                                    <select name="subject_id" class="form-control @error('subject_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Guru</label>
                                    <select id="teacher-select" name="teacher_id" class="form-control select2 @error('teacher_id') is-invalid @enderror" required data-placeholder="-- Pilih Guru --">
                                        <option></option> <!-- Dibiarkan kosong, Select2 akan pakai data-placeholder -->
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->nip }}" {{ old('teacher_id') == $teacher->nip ? 'selected' : '' }}>
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    {{-- <select id="teacher-select" name="teacher_id" class="form-control select2 @error('teacher_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Guru --</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->nip }}" 
                                                    data-nip="{{ $teacher->nip }}"
                                                    {{ old('teacher_id') == $teacher->nip ? 'selected' : '' }}>
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select> --}}
                                    @error('teacher_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Kelas</label>
                                    <select name="class_id" class="form-control @error('class_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }} - {{ $class->parallel_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Tahun Akademik</label>
                                    <select name="academic_year_id" class="form-control @error('academic_year_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Tahun Akademik --</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                                {{ $year->start_year }}/{{ $year->end_year }}
                                                {{ $year->semester == 1 ? '- Ganjil' : '- Genap' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if(count($academicYears) == 0)
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle"></i> Tidak ada data tahun akademik.
                                        Tambahkan tahun akademik terlebih dahulu.
                                    </div>
                                @endif
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-block btn-flat btn-primary">
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
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk dropdown guru
    $('#teacher-select').select2({
        placeholder: $('#teacher-select').data('placeholder'),
        allowClear: true, 
        width: '100%',
        language: {
            inputTooShort: function() {
                return 'Ketik minimal 1 huruf untuk mencari guru';
            },
            noResults: function() {
                return 'Guru tidak ditemukan';
            },
            searching: function() {
                return 'Mencari...';
            }
        }
    });
});
</script>
@endpush