@extends('layouts.app')

@section('title', 'Detail Rekap Kehadiran')
@push('css')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-papm1p5QwDFi1Cdm42Hcps225y7sY9qsJh0kGxHgd6NqBJ38qJNmPR9U1FVLtZL1NVr7DiIP9N6byN1Nsx3X2w=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .recap-table th,
    .recap-table td {
        vertical-align: middle;
        text-align: center;
    }

    .recap-table th {
        background: #0e3b7c;
        color: #fff;
    }

    .selection {
        width: 160px;
    }
</style>
@endpush

@section('content')
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="container-fluid py-4">
                <h4 class="fw-bold mb-3">DETAIL RIWAYAT PRESENSI</h4>
                <div class="card p-3 mb-4" style="border: #0e3b7c 1px solid;">
                    <form class="row gx-2 gy-2 align-items-center" method="GET" action="">
                        <input type="hidden" name="class" value="{{ $selectedClass }}">
                        <input type="hidden" name="month" value="{{ $selectedMonth }}">
                        <input type="hidden" name="year" value="{{ $selectedYear }}">
                        <div class="col-auto d-flex align-items-center">
                            <i class="fa-solid fa-calendar-days me-2"></i>
                            <label for="date" class="form-label mb-1 me-2 mr-2">Tanggal: </label>
                            <select class="form-select select2 selection" id="date" name="date"
                                onchange="this.form.submit()">
                                @foreach ($dates as $date)
                                <option value="{{ $date }}" {{ $selectedDate == $date ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="card bg-primary p-3 d-flex align-items-start justify-content-start">
                    <div class="text-white fw-bold">
                        Rekap Kehadiran {{ optional($classes->find($selectedClass))->name }}, tanggal:
                        {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12 d-flex align-items-center justify-content-center">
                        <div class="col-md-6">
                            <div class="card p-3">
                                <h6 class="fw-bold mb-2">Persentase Kehadiran Siswa</h6>
                                <canvas id="studentAttendanceChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3">
                                <h6 class="fw-bold mb-2">Distribusi Status Kehadiran Siswa</h6>
                                <canvas id="studentAttendanceBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mt-3">
                    <table id="recapTable" class="table table-bordered recap-table mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NISN</th>
                                <th>Nama</th>
                                <th>Jam Masuk</th>
                                <th>Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $i => $student)
                            @php
                            $att = $attendances[$student->nisn] ?? null;
                            $status = $att->status ?? '-';
                            $time = $att
                            ? ($att->time_in
                            ? date('H:i', strtotime($att->time_in))
                            : '-')
                            : '-';
                            $badgeClass =
                            $status === 'Hadir'
                            ? 'text-success'
                            : ($status === 'Absen'
                            ? 'text-danger'
                            : ($status === 'Sakit'
                            ? 'text-info'
                            : ($status === 'Izin'
                            ? 'text-warning'
                            : ($status === 'Terlambat'
                            ? 'text-secondary'
                            : 'text-muted'))));
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $student->nisn }}</td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $time }}</td>
                                <td class="fw-semibold {{ $badgeClass }}">{{ $status }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
            $('#recapTable').DataTable({
                paging: true,
                searching: true,
                ordering: false,
                info: true,
                lengthChange: false,
                columnDefs: [{
                    orderable: false,
                    targets: 0
                }]
            });
            $('#date').select2({
                placeholder: "Pilih Tanggal",
                allowClear: false,
                width: 'resolve'
            });

            // Chart.js - Student Attendance (Pie)
            var ctx = document.getElementById('studentAttendanceChart');
            if (ctx) {
                var chart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir', 'Absen', 'Sakit', 'Izin', 'Terlambat'],
                        datasets: [{
                            data: [
                                {{ $studentStats['Hadir'] ?? 0 }},
                                {{ $studentStats['Absen'] ?? 0 }},
                                {{ $studentStats['Sakit'] ?? 0 }},
                                {{ $studentStats['Izin'] ?? 0 }},
                                {{ $studentStats['Terlambat'] ?? 0 }}
                            ],
                            backgroundColor: [
                                '#28a745', // Hadir
                                '#dc3545', // Absen
                                '#17a2b8', // Sakit
                                '#ffc107', // Izin
                                '#6c757d'  // Terlambat
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': ' + context.parsed + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }
            // Chart.js - Student Attendance (Bar)
            var barCtx = document.getElementById('studentAttendanceBarChart');
            if (barCtx) {
                var barChart = new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Hadir', 'Absen', 'Sakit', 'Izin', 'Terlambat'],
                        datasets: [{
                            label: 'Persentase',
                            data: [
                                {{ $studentStats['Hadir'] ?? 0 }},
                                {{ $studentStats['Absen'] ?? 0 }},
                                {{ $studentStats['Sakit'] ?? 0 }},
                                {{ $studentStats['Izin'] ?? 0 }},
                                {{ $studentStats['Terlambat'] ?? 0 }}
                            ],
                            backgroundColor: [
                                '#28a745', // Hadir
                                '#dc3545', // Absen
                                '#17a2b8', // Sakit
                                '#ffc107', // Izin
                                '#6c757d'  // Terlambat
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Persentase (%)'
                                }
                            }
                        }
                    }
                });
            }
        });
</script>
@endpush