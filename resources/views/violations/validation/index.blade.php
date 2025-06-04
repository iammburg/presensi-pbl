@extends('layouts.app')

@section('title')
    Validasi Laporan Pelanggaran
@endsection

@push('css')
    <style>
        /* Tabel header dan baris sesuai desain gambar */
        .custom-violations-table thead th {
            background-color: #009cf3;
            color: #fff;
            text-align: center;
            vertical-align: middle;
        }
        .custom-violations-table tbody td {
            vertical-align: middle;
            background-color: #fff;
        }
        .custom-violations-table tbody tr:nth-child(odd) {
            background-color: #f8fafd;
        }
        .custom-violations-table tbody tr {
            transition: background 0.2s;
        }
        .custom-violations-table tbody tr:hover {
            background-color: #e6f2fb;
        }
        .custom-violations-table .btn-info {
            background: #ffc107;
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 2px 16px;
            border-radius: 6px;
        }
        .custom-violations-table .btn-info i {
            margin-right: 4px;
        }
        /* Pagination & search style */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 6px;
            margin: 0 2px;
            border: 1px solid #e0e0e0;
            color: #009cf3 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #009cf3 !important;
            color: #fff !important;
        }
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            font-weight: 500;
        }
        .dataTables_wrapper .dataTables_length select {
            min-width: 60px;
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
                        <div class="card-tools">
                            <form method="GET" class="form-inline mb-2">
                                <label class="mr-2">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control form-control-sm mr-2" value="{{ request('tanggal', now()->format('Y-m-d')) }}">
                                <button type="submit" class="btn btn-sm btn-primary">Pilih</button>
                            </form>
                            <form method="GET" class="form-inline" style="gap: 6px;">
                                <input type="hidden" name="tanggal" value="{{ request('tanggal', now()->format('Y-m-d')) }}">
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama, pelanggaran, kelas..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-sm btn-secondary"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
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
                            <table class="table table-bordered table-striped table-hover custom-violations-table">
                                <thead>
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
                                            <td>{{ $violation->student && $violation->student->schoolClass ? $violation->student->schoolClass->name : '-' }}</td>
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
                                                <a href="{{ route('violation-validations.show', $violation->id) }}" class="btn btn-info btn-sm" title="Tindakan">
                                                    Tindakan
                                                </a>
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
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <div>
                                <span>Showing {{ $violations->firstItem() }} to {{ $violations->lastItem() }} of {{ $violations->total() }} entries</span>
                            </div>
                            <div>
                                {{ $violations->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
        $(function () {
            setTimeout(function() {
                $(".alert-dismissible").alert('close');
            }, 5000);
        });
    </script>
@endpush
