@extends('layouts.app')

@section('content')
@if (auth()->user()->hasRole('Guru'))
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0">Selamat datang, {{ ucwords(auth()->user()->name) }}!</h4>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center py-2">
                <h6 class="m-0">REKAP SISWA KEHADIRAN HARI INI</h6>
            </div>
            <div class="card-body p-3" style="background-color: rgba(0, 123, 255, 0.1);">
                @php
                    $data = [
                        ['jam' => 'Jam 1 (07.00 - 07.45)', 'kelas' => 'XI-A', 'hadir' => 25, 'total' => 30],
                        ['jam' => 'Jam 2 - Jam 3 (07.45 - 09.00)', 'kelas' => 'XII-B', 'hadir' => 20, 'total' => 30],
                        ['jam' => 'Jam 2 - Jam 3 (07.45 - 09.00)', 'kelas' => 'XII-B', 'hadir' => 28, 'total' => 30],
                        ['jam' => 'Jam 4 - Jam 5 (09.00 - 10.30)', 'kelas' => 'X-D', 'hadir' => 10, 'total' => 30],
                        ['jam' => 'Jam 4 - Jam 5 (09.00 - 10.30)', 'kelas' => 'X-D', 'hadir' => 10, 'total' => 30],
                        ['jam' => 'Jam 6 - Jam 9 (10.30 - 14.00)', 'kelas' => 'X-F', 'hadir' => 30, 'total' => 30],
                        ['jam' => 'Jam 6 - Jam 9 (10.30 - 14.00)', 'kelas' => 'X-F', 'hadir' => 30, 'total' => 30],
                        ['jam' => 'Jam 6 - Jam 9 (10.30 - 14.00)', 'kelas' => 'X-F', 'hadir' => 30, 'total' => 30],
                        ['jam' => 'Jam 6 - Jam 9 (10.30 - 14.00)', 'kelas' => 'X-F', 'hadir' => 30, 'total' => 30],
                    ];
                @endphp

                @foreach($data as $item)
                    @php
                        $percentage = ($item['hadir'] / $item['total']) * 100;
                    @endphp
                    <div class="mb-3" style="font-size: 0.875rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div><strong>{{ $item['jam'] }}</strong> - {{ $item['kelas'] }}</div>
                            <div>{{ $item['hadir'] }}/{{ $item['total'] }} Siswa</div>
                        </div>
                        <div class="progress" style="height: 16px;">
                            <div class="progress-bar bg-primary" role="progressbar"
                                style="width: {{ $percentage }}%;"
                                aria-valuenow="{{ $percentage }}"
                                aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>
@endif

@if (auth()->user()->hasRole('superadmin'))

{{-- Header --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0">Dashboard</h4>
            </div>
        </div>
    </div>
</div>

{{-- ISI UTAMA --}}
<div class="container-fluid">

    {{-- Statistik Pengguna --}}
    <div class="row g-3 mb-4">
        @php
            $stats = [
                ['label' => 'Jumlah Pengguna', 'count' => 2240, 'icon' => 'fa-users', 'bg' => 'primary'],
                ['label' => 'Jumlah Admin', 'count' => 5, 'icon' => 'fa-user-shield', 'bg' => 'info'],
                ['label' => 'Jumlah Guru', 'count' => 50, 'icon' => 'fa-chalkboard-teacher', 'bg' => 'warning'],
                ['label' => 'Jumlah Orang Tua', 'count' => 1500, 'icon' => 'fa-user-friends', 'bg' => 'primary'],
                ['label' => 'Pengguna Aktif', 'count' => 200, 'icon' => 'fa-check-circle', 'bg' => 'success'],
                ['label' => 'Pengguna Tidak Aktif', 'count' => 30, 'icon' => 'fa-times-circle', 'bg' => 'danger'],
            ];
        @endphp

        @foreach($stats as $stat)
            <div class="col-md-4 col-lg-2">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="mb-2 text-{{ $stat['bg'] }}">
                            <i class="fas {{ $stat['icon'] }} fa-2x"></i>
                        </div>
                        <h5 class="fw-bold">{{ $stat['count'] }}</h5>
                        <p class="text-muted mb-0 small">{{ $stat['label'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Grafik Kinerja Pengguna --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <h6 class="fw-bold">Performance Pengguna</h6>
                    <div style="height: 300px;">
                        <canvas id="userChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Log Aktivitas Pengguna --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="fw-bold">Log Aktivitas Pengguna</h6>
                        <div>
                            <select class="form-select form-select-sm d-inline w-auto me-2">
                                <option selected>Oktober</option>
                                <option>September</option>
                            </select>
                            <select class="form-select form-select-sm d-inline w-auto">
                                <option selected>2025</option>
                                <option>2024</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Pengguna</th>
                                    <th>Role</th>
                                    <th>Aktivitas</th>
                                    <th>Date - Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Garis Keras</td>
                                    <td>Guru</td>
                                    <td>Membuat Laporan Pelanggaran</td>
                                    <td>12.09.2019 - 12.53 PM</td>
                                </tr>
                                <tr>
                                    <td>Mujier</td>
                                    <td>Admin Sekolah</td>
                                    <td>Mengedit data guru</td>
                                    <td>12.09.2019 - 12.53 PM</td>
                                </tr>
                                <tr>
                                    <td>Ahmad Budi</td>
                                    <td>Siswa</td>
                                    <td>Melakukan presensi</td>
                                    <td>12.09.2019 - 12.53 PM</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ChartJS --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('userChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [
                {
                    label: 'Minggu Ini',
                    data: [5, 20, 75, 10, 0, 55, 60, 40, 20, 85, 100, 90],
                    borderColor: '#FFC107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Minggu Lalu',
                    data: [10, 15, 50, 30, 5, 40, 35, 25, 15, 65, 90, 100],
                    borderColor: '#FF5722',
                    backgroundColor: 'rgba(255, 87, 34, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endpush
@endif
@endsection
