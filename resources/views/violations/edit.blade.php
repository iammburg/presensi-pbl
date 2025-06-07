@extends('layouts.app')

@section('title')
    Edit Laporan Pelanggaran
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
    .current-evidence img {
        max-width: 200px;
        max-height: 200px;
        border: 1px solid #ddd;
        padding: 5px;
        margin-top: 10px;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-uppercase">Edit Laporan Pelanggaran</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('violations.index') }}">Laporan Pelanggaran</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('violations.show', $violation->id) }}">Detail</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Formulir Edit Laporan Pelanggaran</h3>
                    </div>
                    <form action="{{ route('violations.update', $violation->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            @if ($errors->any())
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
                                <label for="student_id">Siswa <span class="text-danger">*</span></label>
                                <select name="student_id" id="student_id" class="form-control @error('student_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Siswa --</option>
                                    @if(isset($students) && $students->count() > 0)
                                        @foreach($students as $student)
                                            <option value="{{ $student->nisn }}" {{ old('student_id', $violation->student_id) == $student->nisn ? 'selected' : '' }}>
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
                                <select name="violation_points_id" id="violation_point_id" class="form-control @error('violation_points_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jenis Pelanggaran --</option>
                                     @if(isset($violationPoints) && $violationPoints->count() > 0)
                                        @foreach($violationPoints as $point)
                                            <option value="{{ $point->id }}" {{ old('violation_points_id', $violation->violation_points_id) == $point->id ? 'selected' : '' }}>
                                                {{ $point->violation_type }} (Poin: {{ $point->points }})
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Tidak ada data jenis pelanggaran</option>
                                    @endif
                                </select>
                                @error('violation_points_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="academic_year_id">Tahun Akademik <span class="text-danger">*</span></label>
                                <select name="academic_year_id" id="academic_year_id" class="form-control @error('academic_year_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Tahun Akademik --</option>
                                    @if(isset($academicYears) && $academicYears->count() > 0)
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->start_year }}/{{ $year->end_year }} {{ $year->semester == 0 ? 'Genap' : 'Ganjil' }}</option>
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
                                <input type="date" name="violation_date" id="violation_date" class="form-control @error('violation_date') is-invalid @enderror" value="{{ old('violation_date', $violation->violation_date) }}" required>
                                @error('violation_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Deskripsi / Kronologi / Keterangan Tambahan <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description', $violation->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="penalty">Sanksi/Penalti yang Diberikan (Opsional)</label>
                                <input type="text" name="penalty" id="penalty" class="form-control @error('penalty') is-invalid @enderror" value="{{ old('penalty', $violation->penalty) }}">
                                @error('penalty')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="evidence">Bukti (Opsional, Gambar: jpg, jpeg, png. Maks: 2MB)</label>
                                @if($violation->evidence)
                                <div class="current-evidence mb-2">
                                    <small>Bukti Saat Ini:</small><br>
                                    <a href="{{ Storage::url($violation->evidence) }}" target="_blank">
                                        <img src="{{ Storage::url($violation->evidence) }}" alt="Bukti Pelanggaran">
                                    </a>
                                    <br><small class="form-text text-muted">Kosongkan input file di bawah jika tidak ingin mengganti bukti.</small>
                                </div>
                                @endif
                                <div class="custom-file">
                                    <input type="file" name="evidence" id="evidence" class="custom-file-input @error('evidence') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg">
                                    <label class="custom-file-label" for="evidence">Pilih file baru...</label>
                                </div>
                                <small class="form-text text-muted">Unggah bukti baru jika ingin mengganti. Biarkan kosong jika tidak ada perubahan bukti.</small>
                                @error('evidence')
                                    <span class="invalid-feedback d-block" role="alert"> {{-- d-block untuk custom-file --}}
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <input type="hidden" name="status" value="{{ old('status', $violation->status ?? 'pending') }}">
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('violations.index') }}" class="btn btn-secondary"><i class="fas fa-times mr-1"></i> Batal</a>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
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
<script>
    $(document).ready(function () {
        // Inisialisasi bsCustomFileInput jika tersedia
        if (typeof bsCustomFileInput !== 'undefined') {
            bsCustomFileInput.init();
        } else {
            // Fallback manual jika bsCustomFileInput tidak ada
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName || "Pilih file baru...");
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
                    $(this).next('.custom-file-label').html('Pilih file baru...');
                    return;
                }

                if (fileSize > 2) { // Batas 2MB
                    alert('Ukuran file bukti tidak boleh melebihi 2MB.');
                    $(this).val('');
                    $(this).next('.custom-file-label').html('Pilih file baru...');
                }
            } else {
                 $(this).next('.custom-file-label').html('Pilih file baru...');
            }
        });

        // Untuk dismiss alert otomatis
        setTimeout(function() {
            $(".alert-dismissible").alert('close');
        }, 7000); // Alert akan hilang setelah 7 detik
    });
</script>
@endpush