@extends('layouts.app')

@section('title')
    Validasi Laporan Pelanggaran
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Validasi Laporan Pelanggaran</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Validasi Pelanggaran</li>
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
                        <h3 class="card-title">Data Laporan</h3>
                        <div class="w-100 mt-3 d-flex align-items-center justify-content-end flex-wrap">
                            <form method="GET" class="form-inline" style="gap: 6px;">
                                <input type="date" name="tanggal" class="form-control form-control-sm mr-2" value="{{ request('tanggal') }}">
                                <select name="status" class="form-control form-control-sm mr-2">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Validasi</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable-main" class="table table-bordered table-striped">
                                <thead class="bg-tertiary text-white">
                                    <tr>
                                        <th style="width: 40px;">No</th>
                                        <th>Nama</th>
                                        <th>Kelas</th>
                                        <th>Pelapor</th>
                                        <th>Nama pelanggaran</th>
                                        <th>Jenis pelanggaran</th>
                                        <th>Bukti</th>
                                        <th style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($violations as $index => $violation)
                                        <tr>
                                            <td class="text-center">{{ $index + $violations->firstItem() }}</td>
                                            <td>{{ $violation->student ? $violation->student->name : 'N/A' }}</td>
                                            <td>{{ $violation->student && $violation->student->schoolClass ? $violation->student->schoolClass->parallel_name : '-' }}</td>
                                            <td>{{ $violation->teacher ? $violation->teacher->name : ($violation->reported_by ?: 'N/A') }}</td>
                                            <td>{{ $violation->violationPoint ? $violation->violationPoint->violation_type : 'N/A' }}</td>
                                            <td>{{ $violation->violationPoint ? $violation->violationPoint->violation_level : '-' }}</td>
                                            <td class="text-center">
                                                @if($violation->evidence)
                                                    <a href="{{ Storage::url($violation->evidence) }}" target="_blank" class="btn btn-outline-info btn-sm" title="Lihat Bukti"><i class="fas fa-image"></i></a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @role('Guru BK')
                                                    <a href="{{ route('violation-validations.show', $violation->id) }}" class="btn btn-info btn-sm" title="Detail">
                                                        @if($violation->validation_status === 'pending')
                                                            <span class="badge badge-warning">Menunggu Validasi</span>
                                                        @elseif($violation->validation_status === 'approved')
                                                            <span class="badge badge-success">Disetujui</span>
                                                        @elseif($violation->validation_status === 'rejected')
                                                            <span class="badge badge-danger">Ditolak</span>
                                                        @else
                                                            <span class="badge badge-secondary">-</span>
                                                        @endif
                                                    </a>
                                                @endrole
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                <i class="fas fa-check-circle fa-3x my-2 text-success"></i><br>
                                                Tidak ada laporan pelanggaran yang perlu divalidasi saat ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <!-- DataTables Scripts -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script>
        $(function () {
            $('#datatable-violations-validation').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "paging": false, // Laravel paginasi
                "searching": false, // Search manual di atas
                "ordering": true,
                "info": false
            });
        });
    </script>
@endpush
