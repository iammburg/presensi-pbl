@extends('layouts.app')

@section('title')
    Form Lapor Pelanggaran
@endsection

@push('css')
{{-- Tambahan CSS jika diperlukan --}}
<style>
    .form-group label {
        font-weight: 500;
    }
    .card-header h4, .card-header .card-title { /* Penyesuaian untuk AdminLTE versi berbeda */
        margin-bottom: 0;
    }
    .form-text.text-muted {
        font-size: 0.875em;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-uppercase">Lapor Pelanggaran Baru</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li> {{-- Ganti 'home' jika nama route dashboard berbeda --}}
                    <li class="breadcrumb-item"><a href="{{ route('violations.index') }}">Laporan Pelanggaran</a></li>
                    <li class="breadcrumb-item active">Lapor Baru</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                {{-- Mengubah card-danger menjadi card-primary --}}
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Formulir Laporan Pelanggaran</h3>
                    </div>
                    <form action="{{ route('violations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            @if ($errors->any())
                                {{-- Pesan error tetap menggunakan alert-danger untuk penekanan --}}
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong><i class="fas fa-exclamation-triangle"></i> Terjadi Kesalahan:</strong>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="form-group mb-3">
                                {{-- Tanda * tetap text-danger untuk menandakan wajib --}}
                                <label for="student_id">Siswa <span class="text-danger">*</span></label>
                                <select name="student_id" id="student_id" class="form-control @error('student_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Siswa --</option>
                                    @if(isset($students) && $students->count() > 0)
                                        @foreach($students as $student)
                                            {{-- Pastikan $student->nisn ada dan unik --}}
                                            <option value="{{ $student->nisn }}" {{ old('student_id') == $student->nisn ? 'selected' : '' }}>
                                                {{ $student->name }} (NISN: {{ $student->nisn }})
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Tidak ada data siswa</option>
                                    @endif
                                </select>
                                @error('student_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="violation_point_id">Jenis Pelanggaran <span class="text-danger">*</span></label>
                                <select name="violation_point_id" id="violation_point_id" class="form-control @error('violation_point_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jenis Pelanggaran --</option>
                                     @if(isset($violationPoints) && $violationPoints->count() > 0)
                                        @foreach($violationPoints as $point)
                                            <option value="{{ $point->id }}" {{ old('violation_point_id') == $point->id ? 'selected' : '' }}>
                                                {{ $point->violation_type }} (Poin: {{ $point->points }})
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Tidak ada data jenis pelanggaran</option>
                                    @endif
                                </select>
                                @error('violation_point_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- TAMBAHAN: Input untuk Tahun Akademik --}}
                            <div class="form-group mb-3">
                                <label for="academic_year_id">Tahun Akademik <span class="text-danger">*</span></label>
                                <select name="academic_year_id" id="academic_year_id" class="form-control @error('academic_year_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Tahun Akademik --</option>
                                    @if(isset($academicYears) && $academicYears->count() > 0)
                                        @foreach($academicYears as $year)
                                            {{-- Sesuaikan $year->semester_display jika ada atau format lainnya --}}
                                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                                {{ $year->start_year }}/{{ $year->end_year }} {{-- Asumsi field start_year dan end_year --}}
                                                {{-- @if(isset($year->semester)) (Semester: {{ $year->semester == 1 ? 'Ganjil' : 'Genap' }}) @endif --}}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Tidak ada data tahun akademik</option>
                                    @endif
                                </select>
                                @error('academic_year_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="violation_date">Tanggal Pelanggaran <span class="text-danger">*</span></label>
                                <input type="date" name="violation_date" id="violation_date" class="form-control @error('violation_date') is-invalid @enderror" value="{{ old('violation_date', date('Y-m-d')) }}" required>
                                @error('violation_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Deskripsi / Kronologi / Keterangan Tambahan <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            {{-- TAMBAHAN: Input untuk Sanksi/Penalti (Opsional) --}}
                            <div class="form-group mb-3">
                                <label for="penalty">Sanksi/Penalti yang Diberikan (Opsional)</label>
                                <input type="text" name="penalty" id="penalty" class="form-control @error('penalty') is-invalid @enderror" value="{{ old('penalty') }}">
                                @error('penalty')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="evidence">Bukti (Opsional, Gambar: jpg, jpeg, png. Maks: 2MB)</label>
                                <div class="custom-file">
                                    <input type="file" name="evidence" id="evidence" class="custom-file-input @error('evidence') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                                    <label class="custom-file-label" for="evidence">Pilih file...</label>
                                </div>
                                <small class="form-text text-muted">Jika ada, unggah bukti berupa gambar. Biarkan kosong jika tidak ada.</small>
                                @error('evidence')
                                    <span class="invalid-feedback d-block" role="alert"> {{-- d-block untuk custom-file --}}
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('violations.index') }}" class="btn btn-secondary"><i class="fas fa-times mr-1"></i> Batal</a>
                                {{-- Mengubah btn-danger menjadi btn-primary --}}
                                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane mr-1"></i> Laporkan Pelanggaran</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
{{-- Pastikan jQuery sudah termuat sebelum skrip ini --}}
{{-- Jika Anda menggunakan AdminLTE, jQuery biasanya sudah ada --}}
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}} {{-- Contoh jika jQuery belum ada --}}

{{-- Skrip untuk bs-custom-file-input jika belum di-include global oleh AdminLTE --}}
{{-- Contoh: <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script> --}}
<script>
    $(document).ready(function () {
        // Inisialisasi bsCustomFileInput jika tersedia
        if (typeof bsCustomFileInput !== 'undefined') {
            bsCustomFileInput.init();
        } else {
            // Fallback manual jika bsCustomFileInput tidak ada
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName || "Pilih file...");
            });
        }

        // Validasi ukuran file di sisi client (opsional)
        $('#evidence').on('change', function() {
            if (this.files[0]) {
                const fileSize = this.files[0].size / 1024 / 1024; // in MB
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                const fileType = this.files[0].type;

                if (!allowedTypes.includes(fileType)) {
                    alert('Tipe file bukti harus berupa JPG, JPEG, atau PNG.');
                    $(this).val('');
                    $(this).next('.custom-file-label').html('Pilih file...');
                    return;
                }

                if (fileSize > 2) { // Batas 2MB
                    alert('Ukuran file bukti tidak boleh melebihi 2MB.');
                    $(this).val('');
                    $(this).next('.custom-file-label').html('Pilih file...');
                } else {
                    // Nama file sudah dihandle oleh bsCustomFileInput atau fallback di atas
                }
            } else {
                 $(this).next('.custom-file-label').html('Pilih file...');
            }
        });

        // Untuk dismiss alert otomatis
        setTimeout(function() {
            $(".alert-dismissible").alert('close');
        }, 7000); // Alert akan hilang setelah 7 detik
    });
</script>
@endpush