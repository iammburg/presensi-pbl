@extends('layouts.app')

@section('title', 'Edit Data Siswa')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Edit Data Siswa</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right"></ol>
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
                            <h5 class="card-title m-0">Form Edit Siswa</h5>
                            <div class="card-tools">
                                <a href="{{ route('manage-students.index') }}" class="btn btn-tool" title="Kembali">
                                    <i class="fas fa-arrow-alt-circle-left"></i>
                                </a>
                            </div>
                        </div>
                        <form action="{{ route('manage-students.update', $student->nisn) }}" method="POST"
                            enctype="multipart/form-data" id="edit-student-form" autocomplete="off">
                            @csrf
                            @method('PUT')
                            <div class="card-body">

                                <!-- NIS -->
                                <div class="form-group">
                                    <label for="nis" class="font-weight-bold">NIS</label>
                                    <input type="text" class="form-control @error('nis') is-invalid @enderror"
                                        id="nis" name="nis" value="{{ old('nis', $student->nis) }}" maxlength="25" required>
                                    @error('nis')
                                        <div class="invalid-feedback">
                                            NIS tidak boleh lebih dari 20 karakter.
                                        </div>
                                    @enderror

                                </div>

                                <!-- NISN -->
                                <div class="form-group">
                                    <label for="nisn" class="font-weight-bold">NISN</label>
                                    <input type="text" class="form-control @error('nisn') is-invalid @enderror"
                                        id="nisn" name="nisn" value="{{ old('nisn', $student->nisn) }}" maxlength="10" required
                                        readonly>
                                    @error('nisn')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Nama Lengkap -->
                                <div class="form-group">
                                    <label for="name" class="font-weight-bold">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $student->name) }}">
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Jenis Kelamin -->
                                <div class="form-group">
                                    <label for="gender" class="font-weight-bold">Jenis Kelamin</label>
                                    <select class="form-control @error('gender') is-invalid @enderror" id="gender"
                                        name="gender">
                                        <option value="L" {{ old('gender', $student->gender) == 'L' ? 'selected' : '' }}>
                                            Laki-laki</option>
                                        <option value="P" {{ old('gender', $student->gender) == 'P' ? 'selected' : '' }}>
                                            Perempuan</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Tanggal Lahir -->
                                <div class="form-group">
                                    <label for="birth_date" class="font-weight-bold">Tanggal Lahir</label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                        id="birth_date" name="birth_date" value="{{ old('birth_date', $student->birth_date) }}">
                                    @error('birth_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Alamat -->
                                <div class="form-group">
                                    <label for="address" class="font-weight-bold">Alamat</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                        name="address" rows="3">{{ old('address', $student->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- No. Telepon -->
                                <div class="form-group">
                                    <label for="phone" class="font-weight-bold">No. Telepon</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone', $student->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Nama Orang Tua -->
                                <div class="form-group">
                                    <label for="parent_name" class="font-weight-bold">Nama Orang Tua</label>
                                    <input type="text" class="form-control @error('parent_name') is-invalid @enderror"
                                        id="parent_name" name="parent_name"
                                        value="{{ old('parent_name', $student->parent_name) }}">
                                    @error('parent_name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- No. Telepon Orang Tua -->
                                <div class="form-group">
                                    <label for="parent_phone" class="font-weight-bold">No. Telepon Orang Tua</label>
                                    <input type="text" class="form-control @error('parent_phone') is-invalid @enderror"
                                        id="parent_phone" name="parent_phone"
                                        value="{{ old('parent_phone', $student->parent_phone) }}">
                                    @error('parent_phone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Email Orang Tua -->
                                <div class="form-group">
                                    <label for="parent_email" class="font-weight-bold">Email Orang Tua</label>
                                    <input type="email" class="form-control @error('parent_email') is-invalid @enderror"
                                        id="parent_email" name="parent_email"
                                        value="{{ old('parent_email', $student->parent_email) }}">
                                    @error('parent_email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Tahun Masuk -->
                                <div class="form-group">
                                    <label for="enter_year" class="font-weight-bold">Tahun Masuk</label>
                                    <input type="number" class="form-control @error('enter_year') is-invalid @enderror"
                                        id="enter_year" name="enter_year"
                                        value="{{ old('enter_year', $student->enter_year) }}">
                                    @error('enter_year')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Foto -->
                                <div class="form-group">
                                    <label for="photo" class="font-weight-bold">Foto</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('photo') is-invalid @enderror"
                                            id="photo" name="photo" accept="image/*">
                                        <label class="custom-file-label" for="photo">Pilih file</label>
                                    </div>
                                    <small class="form-text text-muted">Ukuran maksimal 2MB.</small>
                                    @error('photo')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                    <span id="photo-size-error" class="text-danger" style="display:none;">Ukuran foto maksimal 2MB.</span>
                                    @if ($student->photo)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto Siswa"
                                                class="img-thumbnail" style="max-height: 200px">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-block btn-flat text-white"
                                    style="background-color: #1777E5">
                                    <i class="fas fa-save"></i> Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush

@push('js')
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            bsCustomFileInput.init();
            $('#photo').on('input', function() {
                const file = this.files[0];
                if (file && file.size > 2 * 1024 * 1024) {
                    $(this).addClass('is-invalid');
                    $('#photo-size-error').show();
                } else {
                    $(this).removeClass('is-invalid');
                    $('#photo-size-error').hide();
                }
            });
            $('#edit-student-form').on('submit', function(e) {
                const fileInput = document.getElementById('photo');
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    if (file.size > 2 * 1024 * 1024) {
                        $('#photo').addClass('is-invalid');
                        $('#photo-size-error').show();
                        e.preventDefault();
                        return false;
                    }
                }
            });
        });
    </script>
@endpush