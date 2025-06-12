@extends('layouts.app')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Detail Validasi Laporan Pelanggaran</h4>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Pelanggaran</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <td>{{ $violation->student ? $violation->student->name : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Pelanggaran</th>
                                        <td>{{ $violation->violationPoint ? $violation->violationPoint->violation_type : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tingkat</th>
                                        <td>{{ $violation->violationPoint ? $violation->violationPoint->violation_level : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Pelanggaran</th>
                                        <td>{{ $violation->violation_date ? ($violation->violation_date instanceof \Illuminate\Support\Carbon ? $violation->violation_date->format('d/m/Y') : $violation->violation_date) : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tahun Ajaran</th>
                                        <td>{{ $violation->academicYear ? $violation->academicYear->start_year . '/' . $violation->academicYear->end_year . ' ' . ($violation->academicYear->semester == 0 ? 'Genap' : 'Ganjil') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Deskripsi</th>
                                        <td>{{ $violation->description ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Dilaporkan Oleh</th>
                                        <td>{{ $violation->teacher ? $violation->teacher->name : ($violation->reported_by ?: '-') }}</td>
                                    </tr>
                                    @if(!empty($violation->evidence))
                                    <tr>
                                        <th>Bukti</th>
                                        <td>
                                            <a href="{{ asset('storage/' . $violation->evidence) }}" target="_blank" class="btn btn-info btn-sm">Lihat Bukti</a>
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Validasi</h5>
                                <form action="{{ route('violation-validations.validate', ['violation' => $violation->id]) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="validation_status">Status Validasi</label>
                                        <select name="validation_status" id="validation_status" class="form-control">
                                            <option value="approved">Setujui</option>
                                            <option value="rejected">Tolak</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="notes_container">
                                        <label for="validation_notes">Catatan Penolakan <span class="text-danger">*</span></label>
                                        <textarea name="validation_notes" id="validation_notes" rows="3" class="form-control"></textarea>
                                        <small class="text-danger" id="notes_error" style="display:none;">Catatan penolakan wajib diisi jika menolak.</small>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('violation-validations.index') }}" class="btn btn-secondary">Kembali</a>
                                        <button type="submit" class="btn btn-primary">Validasi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('scripts')
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const statusSelect = document.getElementById('validation_status');
        const notesField = document.getElementById('validation_notes');
        const notesError = document.getElementById('notes_error');
        if (statusSelect.value === 'rejected' && notesField.value.trim() === '') {
            notesError.style.display = '';
            notesField.focus();
            e.preventDefault();
        } else {
            notesError.style.display = 'none';
        }
    });
</script>
@endsection
@endsection
