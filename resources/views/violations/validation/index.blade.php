@extends('layouts.app')

@section('title')
    Validasi Laporan Pelanggaran
@endsection

@push('css')
    {{-- DataTables CSS jika Anda menggunakannya di halaman ini --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <style>
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Validasi Laporan Pelanggaran</h4>
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
                        <h3 class="card-title">Data Pelanggaran Menunggu Validasi</h3>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <div class="table-responsive">
                            <table id="datatable-pelanggaran" class="table table-bordered table-striped">
                                <thead class="bg-tertiary text-white">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>Jenis Pelanggaran</th>
                                        <th>Tanggal Pelanggaran</th>
                                        <th>Status</th>
                                        <th>Dilaporkan Oleh</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($violations as $violation)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $violation->student ? $violation->student->name : '-' }}</td>
                                            <td>{{ $violation->violationPoint ? $violation->violationPoint->violation_type : '-' }}</td>
                                            <td>{{ $violation->violation_date ? \Carbon\Carbon::parse($violation->violation_date)->format('d/m/Y') : '-' }}</td>
                                            <td>
                                                @if($violation->validation_status === 'pending')
                                                    <span class="badge badge-warning">Menunggu Validasi</span>
                                                @elseif($violation->validation_status === 'approved')
                                                    <span class="badge badge-success">Disetujui</span>
                                                @else
                                                    <span class="badge badge-danger">Ditolak</span>
                                                @endif
                                            </td>
                                            <td>{{ $violation->teacher ? $violation->teacher->name : ($violation->reported_by ?: '-') }}</td>
                                            <td>
                                                <a href="{{ route('violation-validations.show', $violation->id) }}" class="btn btn-info btn-sm">Detail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-gray-500">
                                                Tidak ada pelanggaran yang perlu divalidasi
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

@push('js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script>
    $(document).ready(function() {
        $.fn.dataTable.ext.errMode = 'none';
        $('#datatable-pelanggaran').DataTable({
            "ordering": true,
            "responsive": true,
            "autoWidth": false,
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "zeroRecords": "Tidak ada data ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Tidak ada data",
                "infoFiltered": "(disaring dari _MAX_ total data)"
            }
        });
    });
    </script>
@endpush
