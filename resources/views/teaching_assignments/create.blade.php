@extends('layouts.app')

@section('title')
    Tambah Data Pengajaran Guru - Mapel
@endsection

@push('css')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.1.0/dist/select2-bootstrap4.min.css" rel="stylesheet" />

{{-- <!-- Select2 JS (pastikan jQuery sudah dimuat sebelumnya) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}


<style>
/* CSS untuk memusatkan Select2 Multiple dan menyesuaikan ukuran */

/* Tambahan agar placeholder sama seperti dropdown biasa */
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #797978 !important;
    color: rgb(183, 55, 55);
}

.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #495057;
    font-style: normal;
}

.select2-container--default .select2-selection--single .select2-selection__arrow b {
    display: none;
}

/* Tambahkan icon caret seperti native select - HANYA untuk single select */
.select2-container--default .select2-selection--single {
    background-image: url("data:image/svg+xml,%3Csvg width='10' height='5' viewBox='0 0 12 5' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%23555' stroke-width='1' fill='none' fill-rule='evenodd'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.2rem center;
    background-size: 12px 14px;
    padding-right: 2rem !important;
    border: 1px solid #ced4da !important;
    min-height: calc(1.5em + 0.75rem + 2px) !important;
}

/* STYLING UNTUK MULTIPLE SELECT - CENTERED */

/* Container utama multiple select */
.select2-container--default .select2-selection--multiple {
    background-image: none !important;
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
    min-height: 37px !important;
    padding: 1px 2px !important;
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    justify-content: left !important;
    gap: 4px !important;
}

/* Area untuk selected items dan search input */
/* .select2-container--default .select2-selection--multiple .select2-selection__rendered {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
} */

/* Styling untuk selected items (tags) */
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #007bff !important;
    color: white !important;
    border: 1px solid #007bff !important;
    border-radius: 0.25rem !important;
    padding: 6px 20px 6px 20px !important;
    margin: 2px !important;
    font-size: 16px !important;
    position: relative !important;
    display: inline-flex !important;
    align-items: flex-end !important;
    justify-content: center !important;
    max-width: none !important;
    white-space: nowrap !important;
    line-height: 1.2 !important;
}

/* Posisikan tombol remove di kanan */
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: rgba(255, 255, 255, 0.8) !important;
    cursor: pointer !important;
    font-weight: bold !important;
    position: absolute !important;
    right: 6px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    margin: 0 !important;
    padding: 0 !important;
    width: 14px !important;
    height: 14px !important;
    text-align: center !important;
    line-height: 14px !important;
    font-size: 12px !important;
    border-radius: 2px !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: white !important;
    background-color: rgba(255, 255, 255, 0.2) !important;
}

/* Container untuk search input */
/* .select2-container--default .select2-selection--multiple .select2-search--inline {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 !important;
    padding: 0 !important;
} */

/* Search input field */
.select2-container--default .select2-selection--multiple .select2-search--inline .select2-search__field {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    background: transparent !important;
    margin: 0 !important;
    padding: 2px 0px !important;
    min-height: 30px !important;
    font-size: 16px !important;
    text-align: left !important;
    min-width: 150px !important;
    width: fit-content !important;
    display: flex;
    align-items: center;
}

/* Placeholder untuk multiple select */
.select2-container--default .select2-selection--multiple .select2-selection__placeholder {
    color: #0088ff !important;
    margin: 0 !important;
    padding: 6px 8px !important;
    text-align: left !important;
    width: 100% !important;
    font-size: 16px !important;
    /* line-height: 1.2 !important; */
}

/* Ketika ada selected items, pusatkan semuanya */
.select2-container--default .select2-selection--multiple:has(.select2-selection__choice) .select2-selection__rendered {
    justify-content: left !important;
}
/* Rounded dropdown list */
.select2-container--default .select2-dropdown {
    border-radius: 12px !important;
    border: .5px solid #abaaa4 !important;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    margin-top: 4.5px !important;
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
    text-align: left;
}

/* Hapus tombol clear (x) dari Select2 single */
.select2-container--default .select2-selection--single .select2-selection__clear {
    display: none !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__clear {
    display: none !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__placeholder {
    color: #0080ff !important;
    font-style: normal !important;
    font-size: 16px !important;
    line-height: 1.5 !important;
}


/* Responsive adjustments */
@media (max-width: 576px) {
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        font-size: 12px !important;
        padding: 4px 20px 4px 8px !important;
    }
    
    .select2-container--default .select2-selection--multiple .select2-search--inline .select2-search__field {
        min-width: 120px !important;
        font-size: 12px !important;
    }
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
                                                {{ $subject->subject_name }}
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

                                    @error('teacher_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="class_id">Kelas</label>
                                    <select name="class_id[]" id="class_id" class="form-control select2-multiple" multiple required data-placeholder="-- Pilih Kelas --">
                                        <option></option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ collect(old('class_id'))->contains($class->id) ? 'selected' : '' }}>
                                                {{ $class->name }} - {{ $class->parallel_name }}
                                            </option>
                                        @endforeach
                                    </select>
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

    // Inisialisasi Select2 untuk multiple select (kelas)
    $('.select2-multiple').select2({
        placeholder: "Pilih satu atau beberapa kelas",
        allowClear: true,
        width: '100%',
        closeOnSelect: false // Jangan tutup dropdown setelah memilih item
    });
});
</script>
@endpush