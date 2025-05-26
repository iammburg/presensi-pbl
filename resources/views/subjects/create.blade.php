@extends('layouts.app')

@push('css')
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Tambah Data Mata Pelajaran</h4>
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
                            <h5 class="card-title m-0">Form Data Mata Pelajaran</h5>
                            <div class="card-tools">
                                <a href="{{ route('manage-subject.index') }}" class="btn btn-tool">
                                    <i class="fas fa-arrow-alt-circle-left"></i>
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('manage-subject.store') }}" method="post">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Kode Mata Pelajaran</label>
                                    <input type="text" name="subject_code"
                                        class="form-control @error('subject_code') is-invalid @enderror"
                                        placeholder="Contoh: MAT01" value="{{ old('subject_code') }}">
                                    @error('subject_code')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Nama Mata Pelajaran</label>
                                    <input type="text" name="subject_name"
                                        class="form-control @error('subject_name') is-invalid @enderror"
                                        placeholder="Contoh: Matematika" value="{{ old('subject_name') }}">
                                    @error('subject_name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label>Nama Kurikulum</label>
                                    <select name="curriculum_name" class="form-control @error('curriculum_name') is-invalid @enderror">
                                        <option value="">-- Pilih Kurikulum --</option>
                                        @php
                                            $kurikulums = [
                                                'Kurikulum 2004 (KBK-Kurikulum Berbasis Kompetensi)',
                                                'Kurikulum 2006 (KTSP-Kurikulum Tingkat Satuan Pendidikan)',
                                                'Kurikulum 2013 (K-13)',
                                                'Kurikulum Darurat 2020',
                                                'Kurikulum Merdeka 2022',
                                                'Kurikulum Merdeka 2024'
                                            ];
                                        @endphp
                                            @foreach ($kurikulums as $item)
                                                 <option value="{{ $item }}"
                                                    {{ old('curriculum_name', isset($subject) ? $subject->curriculum_name : '') == $item ? 'selected' : '' }}>
                                                    {{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('curriculum_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                        placeholder="Masukkan deskripsi mata pelajaran">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- <div class="form-group">
                                    <label>Aktifkan Kurikulum Ini?</label><br>
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ old('is_active') ? 'checked' : '' }}> Ya
                                </div> --}}
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

@push('js')
@endpush