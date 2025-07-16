@extends('layouts.app')
@section('title', 'Poin Prestasi')
@section('content')
<div class="container-fluid py-4 px-5">
    <div class="row mb-3 align-items-center">
        <div class="col-md-8">
            <h3 class="fw-bold mb-2">POIN PRESTASI</h3>
        </div>
    </div>
    <div class="row mb-3 justify-content-end">
        <div class="col-md-4">
            <form method="get" class="d-flex align-items-center justify-content-end gap-2">
                <label for="date" class="me-2 mb-0">Tanggal</label>
                <input type="date" name="date" id="date" value="{{ $date }}" class="form-control form-control-sm w-auto">
                <button type="submit" class="btn btn-primary btn-sm">Pilih</button>
            </form>
        </div>
    </div>
    <div class="card card-primary card-outline shadow-sm">
        <div class="card-header bg-tertiary text-white d-flex align-items-center justify-content-between" style="border-radius: 0.5rem 0.5rem 0 0;">
            <span class="card-title m-0 flex-grow-1 fw-bold">Rekap Data Prestasi - {{ $student->name }}</span>
            <span class="btn btn-info fw-bold px-4 py-2 mb-0" style="font-size: 1.1rem;">TOTAL POIN : {{ $totalPoint }}</span>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="achievementsTable" class="table table-bordered table-hover mb-0">
                    <thead class="bg-tertiary text-white align-middle">
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>TANGGAL PELAPORAN</th>
                            <th>PRESTASI</th>
                            <th>KATEGORI</th>
                            <th>BUKTI</th>
                            <th>POIN</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($achievements as $i => $a)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $a->achievement_date ? $a->achievement_date->format('d/m/Y') : '-' }}</td>
                            <td>{{ $a->achievements_name }}</td>
                            <td>{{ optional($a->achievementPoint)->achievement_category ?? '-' }}</td>
                            <td>
                                @if($a->evidence)
                                    <a href="{{ asset('storage/'.$a->evidence) }}" class="btn btn-success btn-sm" download>Download</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ optional($a->achievementPoint)->points ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
        #achievementsTable thead th {
            vertical-align: middle;
            text-align: center;
        }
        #achievementsTable td, #achievementsTable th {
            font-size: 15px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.2em 0.8em;
        }
    </style>
@endpush

@push('js')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script>
        $(function() {
            $('#achievementsTable').DataTable({
                responsive: true,
                autoWidth: false,
                ordering: false,
                language: {
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data tersedia",
                    "infoFiltered": "(difilter dari total _MAX_ data)",
                    "search": "Cari:",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
                    },
                }
            });
        });
    </script>
@endpush
@endsection
