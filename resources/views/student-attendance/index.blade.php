@extends('layouts.app')

@section('title', 'Riwayat Presensi')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Riwayat Presensi</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <!-- Breadcrumb opsional -->
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <form method="GET" action="{{ route('student.attendance') }}">
                <div class="row mb-3 justify-content-end align-items-end">
                    <div class="col-auto">
                        <label for="tanggal" class="form-label">Filter Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control"
                            value="{{ request('tanggal') }}">
                    </div>
                    <div class="col-auto">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                        {{-- <a href="{{ route('student.attendance') }}" class="btn btn-secondary">Reset</a> --}}
                    </div>
                </div>
            </form>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    {{ auth()->user()->student->name ?? 'Siswa' }}
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="studentAttendanceTable" class="table table-bordered table-striped">
                            <thead class="table-primary text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Kehadiran</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attendances as $attendance)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $attendance->classSchedule->assignment->subject->name ?? '-' }}</td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($attendance->meeting_date)->format('d-m-Y') }}
                                        </td>
                                        <td class="text-center">{{ $attendance->time_in ?? '-' }}</td>
                                        <td class="text-center">{{ $attendance->status ?? '-' }}</td>
                                        <td class="text-center">
                                            @switch($attendance->status)
                                                @case('hadir')
                                                    <span class="badge bg-success">Hadir</span>
                                                @break

                                                @case('sakit')
                                                    <span class="badge bg-warning text-dark">Sakit</span>
                                                @break

                                                @case('izin')
                                                    <span class="badge bg-info text-dark">Izin</span>
                                                @break

                                                @case('alpha')
                                                    <span class="badge bg-danger">Tidak Hadir</span>
                                                @break

                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($attendance->status) }}</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada data presensi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @push('css')
            <!-- DataTables CSS -->
            <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
            <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        @endpush

        @push('js')
            <!-- DataTables JS -->
            <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
            <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

            <script>
                $(document).ready(function() {
                    $('#studentAttendanceTable').DataTable({
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
