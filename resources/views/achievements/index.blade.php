@extends('layouts.app')

@section('title', 'Daftar Prestasi')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Daftar Prestasi</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    {{-- Breadcrumb jika perlu --}}
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
                        <h3 class="card-title">Data Prestasi</h3>
                        <div class="card-tools">
                            <a href="{{ route('achievements.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle"></i> Laporkan Prestasi
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-tertiary text-white">
                                    <tr>
                                        <th>Siswa</th>
                                        <th>Nama Prestasi</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($achievements as $achievement)
                                        <tr>
                                            <td>{{ $achievement->student ? $achievement->student->name : '-' }}</td>
                                            <td>{{ $achievement->achievements_name }}</td>
                                            <td>{{ $achievement->achievement_date->format('d/m/Y') }}</td>
                                            <td>
                                                @if($achievement->validation_status === 'pending')
                                                    <span class="badge badge-warning">Menunggu Validasi</span>
                                                @elseif($achievement->validation_status === 'approved')
                                                    <span class="badge badge-success">Disetujui</span>
                                                @else
                                                    <span class="badge badge-danger">Ditolak</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('achievements.show', $achievement) }}" class="btn btn-info btn-sm">Detail</a>
                                                @if($achievement->validation_status === 'pending')
                                                    <a href="{{ route('achievements.edit', $achievement) }}" class="btn btn-warning btn-sm">Edit</a>
                                                    <form action="{{ route('achievements.destroy', $achievement) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus prestasi ini?')">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                    @can('update_laporan_prestasi')
                                                    <!-- Tombol validasi hanya untuk Guru BK -->
                                                    <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#validateModal-{{ $achievement->id }}">Validasi</button>
                                                    <!-- Modal Validasi -->
                                                    <div class="modal fade" id="validateModal-{{ $achievement->id }}" tabindex="-1" role="dialog" aria-labelledby="validateModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <form action="{{ route('achievements.validate', $achievement->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="validateModalLabel">Validasi Prestasi</h5>
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
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-gray-500">
                                                Belum ada prestasi yang dilaporkan
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $achievements->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
