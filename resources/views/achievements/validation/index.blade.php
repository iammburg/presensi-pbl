@extends('layouts.app')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Validasi Prestasi</h4>
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
                        <h3 class="card-title">Data Prestasi Menunggu Validasi</h3>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <div class="table-responsive">
                            <table id="datatable-prestasi" class="table table-bordered table-striped">
                                <thead class="bg-tertiary text-white">
                                    <tr>
                                        <th>No</th>
                                        <th>Siswa</th>
                                        <th>Nama Prestasi</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Dilaporkan Oleh</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($achievements as $achievement)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $achievement->student ? $achievement->student->name : '-' }}</td>
                                            <td>{{ $achievement->achievements_name }}</td>
                                            <td>{{ $achievement->achievement_date ? $achievement->achievement_date->format('d/m/Y') : '-' }}</td>
                                            <td>
                                                @if($achievement->validation_status === 'pending')
                                                    <span class="badge badge-warning">Menunggu Validasi</span>
                                                @elseif($achievement->validation_status === 'approved')
                                                    <span class="badge badge-success">Disetujui</span>
                                                @else
                                                    <span class="badge badge-danger">Ditolak</span>
                                                @endif
                                            </td>
                                            <td>{{ $achievement->teacher ? $achievement->teacher->name : '-' }}</td>
                                            <td>
                                                <a href="{{ route('achievement-validations.show', $achievement) }}" class="btn btn-info btn-sm">Detail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-gray-500">
                                                Tidak ada prestasi yang perlu divalidasi
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#datatable-prestasi').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Tidak ada data ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(disaring dari _MAX_ total data)"
        },
        responsive: true,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "Semua"]
        ],
        pageLength: 10
    });
});
</script>
@endpush
