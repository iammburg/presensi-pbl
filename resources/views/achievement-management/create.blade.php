@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Tambah Data Prestasi</h4>
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
                            <h5 class="card-title m-0">Form Data Prestasi</h5>
                            <div class="card-tools">
                                <a href="{{ url()->previous() }}" class="btn btn-tool" title="Kembali">
                                    <i class="fas fa-arrow-alt-circle-left"></i>
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('achievement-management.store') }}" method="POST">
                            @csrf
                            <div class="card-body">

                                {{-- Jenis Prestasi --}}
                                <div class="form-group">
                                    <label for="jenis_prestasi">Jenis Prestasi</label>
                                    <select id="jenis_prestasi" name="jenis_prestasi"
                                        class="form-control @error('jenis_prestasi') is-invalid @enderror" required>
                                        <option value="" disabled {{ old('jenis_prestasi') ? '' : 'selected' }}>Pilih jenis prestasi</option>
                                        @php
                                            $jenisOptions = [
                                                'Juara 1 Internasional', 'Juara 2 Internasional', 'Juara 3 Internasional',
                                                'Juara 1 Nasional', 'Juara 2 Nasional', 'Juara 3 Nasional',
                                                'Juara 1 Kota/Kabupaten', 'Juara 2 Kota/Kabupaten', 'Juara 3 Kota/Kabupaten',
                                            ];
                                        @endphp
                                        @foreach ($jenisOptions as $option)
                                            <option value="{{ $option }}" {{ old('jenis_prestasi') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                    @error('jenis_prestasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Kategori Prestasi --}}
                                <div class="form-group">
                                    <label for="kategori_prestasi">Kategori Prestasi</label>
                                    <select id="kategori_prestasi" name="kategori_prestasi"
                                        class="form-control @error('kategori_prestasi') is-invalid @enderror" required>
                                        <option value="" disabled {{ old('kategori_prestasi') ? '' : 'selected' }}>Pilih kategori prestasi</option>
                                        <option value="Akademik" {{ old('kategori_prestasi') == 'Akademik' ? 'selected' : '' }}>Akademik</option>
                                        <option value="Non Akademik" {{ old('kategori_prestasi') == 'Non Akademik' ? 'selected' : '' }}>Non Akademik</option>
                                    </select>
                                    @error('kategori_prestasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Poin --}}
                                <div class="form-group">
                                    <label for="poin">Poin</label>
                                    <input type="number" id="poin" name="poin"
                                        class="form-control @error('poin') is-invalid @enderror"
                                        placeholder="0" value="{{ old('poin') }}" required>
                                    @error('poin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-block btn-flat text-white" style="background-color: #1777E5">
                                    <i class="fa fa-save"></i> Simpan
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
