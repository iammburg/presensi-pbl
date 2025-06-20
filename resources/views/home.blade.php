@extends('layouts.app')

@section('content')
@if (auth()->user()->hasRole('Guru'))
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="m-0">Selamat datang, {{ ucwords(auth()->user()->name) }}!</h4>
            </div>
            <div class="col-sm-6">
                <form method="GET" action="" class="float-right">
                    <div class="input-group">
                        <input type="date" 
                               name="date" 
                               class="form-control form-control-sm" 
                               value="{{ $selectedDate }}"
                               max="{{ now()->toDateString() }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center py-2">
                <h6 class="m-0">REKAP SISWA KEHADIRAN - {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</h6>
            </div>

            <div class="card-body p-3" style="background-color: rgba(0, 123, 255, 0.1);">
                @if($data->count())
                    @php 
                        $totalHadir = $data->sum('hadir');
                        $totalTerlambat = $data->sum('terlambat');
                        $totalTidakHadir = $data->sum('tidak_hadir');
                    @endphp
                    
                    @foreach($data as $item)
                        @php
                            $percentage = ($item->total > 0) ? round(($item->hadir / $item->total) * 100, 2) : 0;
                            $terlambatPercentage = ($item->total > 0) ? round(($item->terlambat / $item->total) * 100, 2) : 0;
                            $tidakHadirPercentage = ($item->total > 0) ? round(($item->tidak_hadir / $item->total) * 100, 2) : 0;
                        @endphp

                        <div class="mb-3 attendance-item">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <strong>{{ $item->jam_pelajaran }}</strong> - 
                                    <span class="text-primary">Kelas {{ $item->kelas }}</span>
                                </div>
                                <div>
                                    <span class="badge bg-success mr-1">
                                        {{ $item->hadir }}/{{ $item->total }} Hadir ({{ $percentage }}%)
                                    </span>
                                    @if ($item->terlambat > 0)
                                        <span class="badge bg-warning text-dark">
                                            {{ $item->terlambat }} Terlambat ({{ $terlambatPercentage }}%)
                                        </span>
                                    @endif
                                    @if ($item->tidak_hadir > 0)
                                        <span class="badge bg-danger ml-1">
                                            {{ $item->tidak_hadir }} Tidak Hadir ({{ $tidakHadirPercentage }}%)
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" 
                                     style="width: {{ $percentage }}%"
                                     title="Hadir: {{ $percentage }}%">
                                </div>
                                <div class="progress-bar bg-warning" 
                                     style="width: {{ $terlambatPercentage }}%"
                                     title="Terlambat: {{ $terlambatPercentage }}%">
                                </div>
                                <!-- Tidak Hadir portion left blank as requested -->
                            </div>
                        </div>
                    @endforeach

                    <!-- Moved total counts here with new styling -->
                    <div class="mt-4 text-center">
                        <span class="badge bg-success mr-2">Total Hadir: {{ $totalHadir }}</span>
                        <span class="badge bg-warning text-dark mr-2">Total Terlambat: {{ $totalTerlambat }}</span>
                        <span class="badge bg-danger">Total Tidak Hadir: {{ $totalTidakHadir }}</span>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Tidak ada data kehadiran untuk tanggal {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

@if (auth()->user()->hasRole('superadmin'))
{{-- Header --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h4 class="m-0">Dashboard Superadmin</h4>
            </div>
            <div class="col-sm-6">
                {{-- Form Filter Tahun dan Bulan --}}
                <form method="GET" action="{{ route('home') }}" class="float-sm-right">
                    <div class="input-group">
                        <select name="month" class="form-control form-control-sm">
                            <option value="">Semua Bulan</option>
                            @foreach(range(1, 12) as $month)
                                <option value="{{ $month }}" {{ $month == $selectedMonth ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                        <select name="year" class="form-control form-control-sm">
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-sm btn-primary" type="submit">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ISI UTAMA --}}
<div class="container-fluid">
    
    {{-- =============================================================== --}}
    {{-- === BLOK STATISTIK PENGGUNA (KODE BARU DENGAN SATU TEPIAN) === --}}
    {{-- =============================================================== --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row">
                        @php
                            $stats = [
                                ['label' => 'Jumlah Pengguna', 'count' => $userStats['total_users'], 'icon' => 'fa-users', 'color' => 'primary'],
                                ['label' => 'Jumlah Admin', 'count' => $userStats['total_admins'], 'icon' => 'fa-user-shield', 'color' => 'info'],
                                ['label' => 'Jumlah Guru', 'count' => $userStats['total_teachers'], 'icon' => 'fa-chalkboard-teacher', 'color' => 'warning'],
                                ['label' => 'Jumlah Siswa', 'count' => $userStats['total_students'], 'icon' => 'fa-user-graduate', 'color' => 'primary'],
                            ];
                        @endphp

                        @foreach($stats as $stat)
                            {{-- Setiap item statistik kini menjadi kolom di dalam satu kartu besar --}}
                            <div class="col-6 col-md-3 text-center p-3">
                                <div class="mb-2 text-{{ $stat['color'] }}">
                                    <i class="fas {{ $stat['icon'] }} fa-3x"></i>
                                </div>
                                <h4 class="fw-bold mb-1 text-{{ $stat['color'] }}">{{ $stat['count'] }}</h4>
                                <p class="text-muted small mb-0">{{ $stat['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- =============================================================== --}}
    {{-- =================== AKHIR BLOK STATISTIK ====================== --}}
    {{-- =============================================================== --}}


    {{-- Grafik Kinerja Pengguna (Grafik Baru dengan 4 Garis) --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <h6 class="fw-bold">Statistik Pengguna Baru Tahun {{ $selectedYear }}</h6>
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
                        <h6 class="fw-bold">Aktivitas Terkini</h6>
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
                                @forelse($activityLogs as $log)
                                <tr>
                                    <td>{{ $log['user'] }}</td>
                                    <td>{{ $log['role'] }}</td>
                                    <td>{{ $log['activity'] }}</td>
                                    <td>{{ $log['time'] }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada aktivitas pada periode ini.</td>
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

{{-- ChartJS (Diperbarui untuk 4 Garis Data) --}}
@push('js')
{{-- Tidak ada lagi CSS untuk hover --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('userChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartData['labels']),
            datasets: [
                {
                    label: 'Pengguna',
                    data: @json($chartData['datasets']['users']),
                    borderColor: '#007bff', // Primary
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Admin',
                    data: @json($chartData['datasets']['admins']),
                    borderColor: '#17a2b8', // Info
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Guru',
                    data: @json($chartData['datasets']['teachers']),
                    borderColor: '#ffc107', // Warning
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Siswa',
                    data: @json($chartData['datasets']['students']),
                    borderColor: '#28a745', // Menggunakan warna hijau (Success) agar tidak sama
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endpush
@endif
@endsection