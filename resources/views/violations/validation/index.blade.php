@extends('layouts.app')

@section('title')
    Validasi Laporan Pelanggaran
@endsection

@push('css')
    {{-- DataTables CSS jika Anda menggunakannya di halaman ini --}}
    {{-- <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}"> --}}
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
            <div class="col-sm-6">
                <h1 class="m-0 text-uppercase">Validasi Laporan Pelanggaran</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li> {{-- Sesuaikan route home --}}
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
                <div class="card card-purple card-outline"> {{-- Warna kartu disesuaikan untuk validasi --}}
                    <div class="card-header">
                        <h3 class="card-title">Data Pelanggaran Menunggu Validasi</h3>
                        {{-- Card tools bisa ditambahkan jika ada filter atau aksi lain --}}
                        {{-- <div class="card-tools">
                            <a href="#" class="btn btn-tool" title="Filter Data">
                                <i class="fas fa-filter"></i>
                            </a>
                        </div> --}}
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="bg-purple text-white"> {{-- Warna header tabel disesuaikan --}}
                                    <tr>
                                        <th style="width: 10px;">No</th>
                                        <th>Nama Siswa</th>
                                        <th>Jenis Pelanggaran</th>
                                        <th>Poin</th>
                                        <th>Tanggal Pelanggaran</th>
                                        <th>Dilaporkan Oleh</th>
                                        <th style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($violations as $index => $violation)
                                        <tr>
                                            <td>{{ $index + $violations->firstItem() }}</td>
                                            <td>
                                                {{ $violation->student ? $violation->student->name : 'N/A' }}
                                                @if($violation->student && $violation->student->nisn)
                                                    <br><small class="text-muted">NISN: {{ $violation->student->nisn }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $violation->violationPoint ? $violation->violationPoint->violation_type : 'N/A' }}</td>
                                            <td class="text-center">{{ $violation->violationPoint ? $violation->violationPoint->points : '-' }}</td>
                                            <td>{{ $violation->violation_date ? \Carbon\Carbon::parse($violation->violation_date)->isoFormat('DD MMM YY') : '-' }}</td>
                                            <td>{{ $violation->teacher ? $violation->teacher->name : ($violation->reported_by ?: 'N/A') }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('violation-validations.show', $violation->id) }}" class="btn btn-info btn-sm" title="Lihat Detail & Validasi">
                                                    <i class="fas fa-search-plus"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                <i class="fas fa-check-circle fa-3x my-2 text-success"></i><br>
                                                Tidak ada laporan pelanggaran yang perlu divalidasi saat ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-center">
                            {{ $violations->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    {{-- DataTables JS jika Anda menggunakannya --}}
    {{-- <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script> --}}
    <script>
        $(function () {
            // Jika Anda menggunakan DataTables, inisialisasi di sini
            // $("#violationsValidationTable").DataTable({
            //     "responsive": true, "lengthChange": false, "autoWidth": false,
            //     "paging": false, // Jika paginasi Laravel yang dipakai
            //     "searching": true,
            //     "ordering": true,
            //     "info": false, // Jika paginasi Laravel yang dipakai
            // });

            // Auto-dismiss alert
            setTimeout(function() {
                $(".alert-dismissible").alert('close');
            }, 5000); // Alert akan hilang setelah 5 detik
        });
    </script>
@endpush
