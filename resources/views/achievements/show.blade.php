@extends('layouts.app')

@section('title', 'Detail Prestasi')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Detail Prestasi</h4>
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
                                        <td>{{ $achievement->student->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Prestasi</th>
                                        <td>{{ $achievement->achievements_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Prestasi</th>
                                        <td>{{ $achievement->achievementPoint->jenis_prestasi ?? '-' }} ({{ $achievement->achievementPoint->poin ?? '-' }} poin)</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Prestasi</th>
                                        <td>{{ $achievement->achievement_date->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tahun Ajaran</th>
                                        <td>
                                            {{ $achievement->academicYear ? $achievement->academicYear->start_year . '/' . $achievement->academicYear->end_year . ' ' . ($achievement->academicYear->semester == 0 ? 'Ganjil' : 'Genap') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Deskripsi</th>
                                        <td>{{ $achievement->description }}</td>
                                    </tr>
                                    <tr>
                                        <th>Dilaporkan Oleh</th>
                                        <td>{{ $achievement->teacher->name }}</td>
                                    </tr>
                                    @if($achievement->evidence)
                                    <tr>
                                        <th>Bukti</th>
                                        <td><a href="{{ asset('storage/' . $achievement->evidence) }}" target="_blank" class="btn btn-info btn-sm">Lihat Bukti</a></td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Status Validasi</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($achievement->validation_status === 'pending')
                                                <span class="badge badge-warning">Menunggu Validasi</span>
                                            @elseif($achievement->validation_status === 'approved')
                                                <span class="badge badge-success">Disetujui</span>
                                            @else
                                                <span class="badge badge-danger">Ditolak</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($achievement->validation_status !== 'pending')
                                    <tr>
                                        <th>Hasil Validasi</th>
                                        <td>
                                            @if($achievement->validation_status === 'approved')
                                                <span class="badge badge-success">Disetujui</span>
                                            @else
                                                <span class="badge badge-danger">Ditolak</span>
                                            @endif
                                            <br>
                                            <small>Oleh: {{ $achievement->validator->name ?? '-' }} pada {{ $achievement->validated_at ? $achievement->validated_at->format('d/m/Y H:i') : '-' }}</small>
                                            @if($achievement->validation_notes)
                                                <br><b>Catatan:</b> {{ $achievement->validation_notes }}
                                            @endif
                                        </td>
                                    </tr>
                                    @if($achievement->validation_status === 'rejected')
                                    <tr>
                                        <th>Catatan Penolakan</th>
                                        <td>{{ $achievement->validation_notes }}</td>
                                    </tr>
                                    @endif
                                    @endif
                                </table>
                            </div>
                        </div>
                        <div class="mt-4 text-right">
                            <a href="{{ route('achievements.index') }}" class="btn btn-secondary">Kembali</a>
                            @if($achievement->validation_status === 'pending')
                                <a href="{{ route('achievements.edit', $achievement) }}" class="btn btn-warning">Edit</a>
                                <form action="{{ route('achievements.destroy', $achievement) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus prestasi ini?')">
                                        Hapus
                                    </button>
                                </form>
                                @can('update_laporan_prestasi')
                                <!-- Tombol validasi hanya untuk Guru BK -->
                                <button class="btn btn-success" data-toggle="modal" data-target="#validateModalShow-{{ $achievement->id }}">Validasi</button>
                                <!-- Modal Validasi -->
                                <div class="modal fade" id="validateModalShow-{{ $achievement->id }}" tabindex="-1" role="dialog" aria-labelledby="validateModalLabelShow" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form action="{{ route('achievements.validate', $achievement->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="validateModalLabelShow">Validasi Prestasi</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Status Validasi</label>
                                                        <select name="validation_status" class="form-control" required>
                                                            <option value="approved">Setujui</option>
                                                            <option value="rejected">Tolak</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Catatan (Opsional)</label>
                                                        <textarea name="validation_notes" class="form-control" rows="2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
