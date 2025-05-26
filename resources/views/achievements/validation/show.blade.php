@extends('layouts.app')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Detail Validasi Prestasi</h4>
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
                        <h3 class="card-title">Informasi Prestasi</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <td>{{ $achievement->student ? $achievement->student->name : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Prestasi</th>
                                        <td>{{ $achievement->achievements_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Prestasi</th>
                                        <td>{{ $achievement->achievementPoint->achievement_type ?? '-' }} ({{ $achievement->achievementPoint->points ?? '-' }} poin)</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Prestasi</th>
                                        <td>{{ $achievement->achievement_date ? ($achievement->achievement_date instanceof \Illuminate\Support\Carbon ? $achievement->achievement_date->format('d/m/Y') : $achievement->achievement_date) : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tahun Ajaran</th>
                                        <td>
                                            {{ $achievement->academicYear ? $achievement->academicYear->start_year . '/' . $achievement->academicYear->end_year . ' ' . ($achievement->academicYear->semester == 0 ? 'Ganjil' : 'Genap') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Deskripsi</th>
                                        <td>{{ $achievement->description ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Dilaporkan Oleh</th>
                                        <td>{{ $achievement->teacher ? $achievement->teacher->name : '-' }}</td>
                                    </tr>
                                    @if(!empty($achievement->evidence))
                                    <tr>
                                        <th>Bukti</th>
                                        <td>
                                            <a href="{{ asset('storage/' . $achievement->evidence) }}" target="_blank" class="btn btn-info btn-sm">Lihat Bukti</a>
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Validasi</h5>
                                <form action="{{ route('achievement-validations.validate', ['achievement' => $achievement->id]) }}" method="POST">
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
                                        <a href="{{ route('achievement-validations.index') }}" class="btn btn-secondary">Kembali</a>
                                        <button type="submit" class="btn btn-primary">Validasi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Debug output dihapus --}}
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
