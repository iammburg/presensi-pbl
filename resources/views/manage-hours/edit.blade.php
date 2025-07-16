@extends('layouts.app')
@push('css')
@endpush

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Edit Jam</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    </ol>
                </div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Edit Jam</h4>
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
                            <h5 class="card-title m-0">Form Edit Jam</h5>
                            <div class="card-tools">
                                <a href="{{ route('manage-hours.index') }}" class="btn btn-tool">
                                    <i class="fas fa-arrow-alt-circle-left"></i>
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('manage-hours.update', $hour->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                {{-- ⬇️ Checkbox di dalam card-body --}}

                                {{-- Tipe Jam --}}
                                <div class="form-group">
                                    <label>Tipe Jam</label>
                                    <select name="session_type"
                                        class="form-control @error('session_type') is-invalid @enderror" required>
                                        <option value="Jam pelajaran"
                                            {{ old('session_type', $hour->session_type) == 'Jam pelajaran' ? 'selected' : '' }}>
                                            Jam Pelajaran</option>
                                        <option value="Jam istirahat"
                                            {{ old('session_type', $hour->session_type) == 'Jam istirahat' ? 'selected' : '' }}>
                                            Jam Istirahat</option>
                                    </select>
                                    @error('session_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Jam ke- --}}
                                <div class="form-group">
                                    <label>Jam ke-</label>
                                    <select name="slot_number"
                                        class="form-control @error('slot_number') is-invalid @enderror" required>
                                        <option value="">-- Pilih Jam ke- --</option>
                                        @for ($i = 1; $i <= 15; $i++)
                                            <option value="{{ $i }}"
                                                {{ old('slot_number', $hour->slot_number) == $i ? 'selected' : '' }}>
                                                Jam ke-{{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('slot_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Jam Mulai & Selesai --}}
                                <div class="form-group">
                                    <label>Jam Mulai</label>
                                    <input type="time" name="start_time"
                                        class="form-control @error('start_time') is-invalid @enderror"
                                        value="{{ old('start_time', $hour->start_time) }}" required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Jam Selesai</label>
                                    <input type="time" name="end_time"
                                        class="form-control @error('end_time') is-invalid @enderror"
                                        value="{{ old('end_time', $hour->end_time) }}" required>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group form-check">
                                    <input type="checkbox" name="is_friday" value="1" class="form-check-input"
                                        id="chkFridayEdit" {{ old('is_friday', $hour->is_friday) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="chkFridayEdit">
                                        Berlaku untuk Hari Jumat
                                    </label>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-block btn-flat text-white" style="background:#1777E5">
                                    <i class="fa fa-save"></i> Update
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
