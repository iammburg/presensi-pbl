@extends('layouts.app')

@section('content')
    @if (Auth::user()->hasRole('Admin Sekolah'))
        <style>
            body {
                background-color: #f8f9fc;
            }

            .dashboard-title {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 20px;
            }

            .summary-card {
                border-radius: 12px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                padding: 10px;
                color: white;
                font-weight: bold;
                font-size: 18px;
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 20px;
            }

            .summary-icon {
                background-color: white;
                border-radius: 50%;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 15px;
                color: #333;
            }

            .card-container {
                width: 100%;
                background-color: white;
                border-radius: 15px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                padding: 25px;
            }

            .legend {
                font-size: 14px;
                color: #555;
                margin-top: 10px;
            }
        </style>

        <div class="container-fluid py-4 px-5">
            <h4 class="dashboard-title">Selamat Datang {{ ucwords(auth()->user()->name) }}!</h4>
            <div class="row">
                {{-- Kartu Statistik --}}
                <div class="col-md-3">
                    <div class="summary-card bg-primary">
                        <div class="summary-icon"><i class="fas fa-users"></i></div>
                        <div>
                            <span style="font-size: 16px;">SISWA AKTIF: </span>
                            <br>
                            <span style="font-size: 16px;">{{ $activeStudents }}</span>
                        </div>
                    </div>
                    <div class="summary-card bg-success">
                        <div class="summary-icon"><i class="fas fa-check"></i></div>
                        <div>
                            <span style="font-size: 16px;">HADIR: </span>
                            <br>
                            <span style="font-size: 16px;">{{ $present }}</span>
                        </div>
                    </div>
                    <div class="summary-card bg-danger">
                        <div class="summary-icon"><i class="fas fa-times"></i></div>
                        <div>
                            <span style="font-size: 16px;">ALPHA: </span>
                            <br>
                            <span style="font-size: 16px;">{{ $absent }}</span>
                        </div>
                    </div>
                    <div class="summary-card bg-info text-dark">
                        <div class="summary-icon"><i class="fas fa-home"></i></div>
                        <div>
                            <span style="font-size: 16px;">IZIN: </span>
                            <br>
                            <span style="font-size: 16px;">{{ $excused }}</span>
                        </div>
                    </div>
                    <div class="summary-card bg-warning text-dark">
                        <div class="summary-icon"><i class="fas fa-clinic-medical"></i></div>
                        <div>
                            <span style="font-size: 16px;">SAKIT: </span>
                            <br>
                            <span style="font-size: 16px;">{{ $excused }}</span>
                        </div>
                    </div>
                </div>


                {{-- Grafik Presensi --}}
                <div class="col-md-9">
                    <div class="card-container">
                        <form method="GET" class="mb-3">
                            <label for="weekFilter" class="form-label">Pilih Minggu:</label>
                            <input type="week" name="week" id="weekFilter" class="form-control w-auto d-inline-block"
                                value="{{ $weekIso }}">
                            <button type="submit" class="btn btn-primary ms-2">Tampilkan</button>
                        </form>

                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Jumlah Kehadiran per Kelas ({{ $startOfWeek->format('d M') }} -
                                    {{ $endOfWeek->format('d M Y') }})</h5>
                                <canvas id="attendanceChart" height="100"></canvas>
                            </div>
                        </div>
                        {{-- <canvas id="attendanceChart" height="60"></canvas> --}}
                        <div class="legend text-center">
                            <i class="fas fa-square" style="color:#2c3e50; font-size: 14px;"></i>
                            Jumlah Presensi Siswa Setiap Kelas
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (Auth::user()->hasRole('Siswa'))
        <style>
            .dashboard-title {
                font-size: 28px;
                font-weight: bold;
                margin-bottom: 20px;
            }

            .pie-chart-container {
                width: 320px;
                margin: 0 auto 24px auto;
            }

            .dashboard-section {
                background: #fffaf0;
                border-radius: 16px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
                padding: 18px 24px 8px 24px;
                margin-bottom: 18px;
            }

            .dashboard-table-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 16px;
                margin-bottom: 4px;
            }

            .see-detail-link {
                color: #bfa14a;
                font-weight: 600;
                text-decoration: underline;
                cursor: pointer;
                font-size: 15px;
            }
        </style>
        <div class="container-fluid py-4 px-5">
            <h4 class="dashboard-title">DASHBOARD</h4>
            <div class="row">
                <div class="col-md-5">
                    <div class="pie-chart-container">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="dashboard-section mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">Prestasi</span>
                            <a href="{{ route('student.achievements') }}" class="see-detail-link">Lihat Detail
                                &gt;&gt;</a>
                        </div>
                        @foreach ($prestasiList as $item)
                            <div class="dashboard-table-row">
                                <span>{{ $item['name'] }}</span>
                                <span>{{ $item['point'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="dashboard-section">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">Pelanggaran</span>
                            <a href="{{ route('student.violations') }}" class="see-detail-link">Lihat Detail
                                &gt;&gt;</a>
                        </div>
                        @foreach ($pelanggaranList as $item)
                            <div class="dashboard-table-row">
                                <span>{{ $item['name'] }}</span>
                                <span>{{ $item['point'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <h4 class="dashboard-title">Performa Kehadiran</h4>
                <canvas id="kehadiranChart"></canvas>
            </div>
        </div>
    @endif
    @if (Auth::user()->hasRole('Guru') && Auth::user()->roles->count() === 1)
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4 class="m-0">Selamat datang, {{ ucwords(auth()->user()->name) }}!</h4>
                    </div>
                    <div class="col-sm-6">
                        <form method="GET" action="" class="float-right">
                            <div class="input-group">
                                <input type="date" name="date" class="form-control form-control-sm"
                                    value="{{ $selectedDate }}" max="{{ now()->toDateString() }}">
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
                        <h6 class="m-0">REKAP SISWA KEHADIRAN -
                            {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</h6>
                    </div>

                    <div class="card-body p-3" style="background-color: rgba(0, 123, 255, 0.1);">
                        @if ($data->count())
                            @php
                                $totalHadir = $data->sum('hadir');
                                $totalTerlambat = $data->sum('terlambat');
                                $totalTidakHadir = $data->sum('tidak_hadir');
                            @endphp

                            @foreach ($data as $item)
                                @php
                                    $percentage = $item->total > 0 ? round(($item->hadir / $item->total) * 100, 2) : 0;
                                    $terlambatPercentage =
                                        $item->total > 0 ? round(($item->terlambat / $item->total) * 100, 2) : 0;
                                    $tidakHadirPercentage =
                                        $item->total > 0 ? round(($item->tidak_hadir / $item->total) * 100, 2) : 0;
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
                                                    {{ $item->tidak_hadir }} Tidak Hadir
                                                    ({{ $tidakHadirPercentage }}%)
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" style="width: {{ $percentage }}%"
                                            title="Hadir: {{ $percentage }}%">
                                        </div>
                                        <div class="progress-bar bg-warning" style="width: {{ $terlambatPercentage }}%"
                                            title="Terlambat: {{ $terlambatPercentage }}%">
                                        </div>
                                        <!-- Tidak Hadir portion left blank as requested -->
                                    </div>
                                </div>
                            @endforeach

                            <!-- Moved total counts here with new styling -->
                            <div class="mt-4 text-center">
                                <span class="badge bg-success mr-2">Total Hadir: {{ $totalHadir }}</span>
                                <span class="badge bg-warning text-dark mr-2">Total Terlambat:
                                    {{ $totalTerlambat }}</span>
                                <span class="badge bg-danger">Total Tidak Hadir: {{ $totalTidakHadir }}</span>
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Tidak ada data kehadiran untuk tanggal
                                {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (Auth::user()->hasRole('superadmin'))
        {{-- Header --}}
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-6">
                        <h4 class="m-0">Dashboard Super Admin</h4>
                    </div>
                    <div class="col-sm-6">
                        {{-- Form Filter Tahun dan Bulan --}}
                        <form method="GET" action="{{ route('home') }}" class="float-sm-right">
                            <div class="input-group">
                                <select name="month" class="form-control form-control-sm">
                                    <option value="">Semua Bulan</option>
                                    @foreach (range(1, 12) as $month)
                                        <option value="{{ $month }}"
                                            {{ $month == $selectedMonth ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="year" class="form-control form-control-sm">
                                    @foreach ($availableYears as $year)
                                        <option value="{{ $year }}"
                                            {{ $year == $selectedYear ? 'selected' : '' }}>
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
                                        [
                                            'label' => 'Jumlah Pengguna',
                                            'count' => $userStats['total_users'],
                                            'icon' => 'fa-users',
                                            'color' => 'primary',
                                        ],
                                        [
                                            'label' => 'Jumlah Admin',
                                            'count' => $userStats['total_admins'],
                                            'icon' => 'fa-user-shield',
                                            'color' => 'info',
                                        ],
                                        [
                                            'label' => 'Jumlah Guru',
                                            'count' => $userStats['total_teachers'],
                                            'icon' => 'fa-chalkboard-teacher',
                                            'color' => 'warning',
                                        ],
                                        [
                                            'label' => 'Jumlah Siswa',
                                            'count' => $userStats['total_students'],
                                            'icon' => 'fa-user-graduate',
                                            'color' => 'primary',
                                        ],
                                    ];
                                @endphp

                                @foreach ($stats as $stat)
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
                                                <td colspan="4" class="text-center">Tidak ada aktivitas pada
                                                    periode ini.</td>
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
                        datasets: [{
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

    @if (Auth::user()->hasRole('Guru BK'))
        <style>
            .dashboard-title {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 30px;
                color: #1a3b6d;
            }

            .dashboard-section {
                max-width: 1000px;
                margin: 0 auto;
            }

            .point-title {
                text-align: center;
                font-size: 24px;
                font-weight: bold;
                color: #1a3b6d;
                margin-bottom: 30px;
                margin-top: 20px;
            }

            .pie-chart-container {
                margin-bottom: 40px;
                text-align: center;
            }

            .badge {
                font-size: 14px;
            }

            .badge-success {
                background-color: #2ecc71;
            }

            .badge-danger {
                background-color: #e74c3c;
            }

            .student-card {
                background-color: #f9f9f7;
                border-radius: 10px;
                padding: 20px;
                margin-bottom: 25px;
            }

            .student-card-header {
                display: flex;
                justify-content: space-between;
                padding-bottom: 10px;
                margin-bottom: 10px;
                border-bottom: 1px solid #eee;
                color: #555;
            }

            .student-card-header a {
                color: #007bff;
                text-decoration: none;
            }

            .student-card-header a:hover {
                text-decoration: underline;
            }

            .student-list {
                margin: 0;
                padding: 0;
                list-style-type: none;
            }

            .student-item {
                display: flex;
                justify-content: space-between;
                padding: 8px 0;
            }

            .student-name {
                flex: 2;
            }

            .student-class,
            .student-points {
                flex: 1;
                text-align: center;
            }

            .point-number {
                display: inline-block;
                background-color: #e74c3c;
                color: white;
                border-radius: 50%;
                width: 30px;
                height: 30px;
                text-align: center;
                line-height: 30px;
            }

            .achievement .point-number {
                background-color: #2ecc71;
            }
        </style>

        <div class="container-fluid py-4 px-5">
            <h1 class="dashboard-title">DASHBOARD</h1>

            <div class="dashboard-section">
                <h2 class="point-title">POINT SISWA TERBANYAK</h2>

                <div class="pie-chart-container">
                    @php
                        // Hitung total poin prestasi dan pelanggaran
                        $totalAchievementPoints = $topAchievementStudents->sum('total_point') ?: 0;
                        $totalViolationPoints = $topViolationStudents->sum('total_point') ?: 0;
                        $totalPoints = $totalAchievementPoints + $totalViolationPoints;

                        // Hitung persentase
                        $achievementPercentage = $totalPoints > 0 ? round(($totalAchievementPoints / $totalPoints) * 100) : 0;
                        $violationPercentage = $totalPoints > 0 ? round(($totalViolationPoints / $totalPoints) * 100) : 0;
                    @endphp

                    <div style="height: 300px; width: 300px; margin: 0 auto;">
                        <canvas id="pointPieChart"></canvas>
                    </div>

                    <div class="text-center mt-4">
                        <div class="d-inline-block mx-3">
                            <span class="badge badge-success p-2">Prestasi: {{ $achievementPercentage }}%</span>
                        </div>
                        <div class="d-inline-block mx-3">
                            <span class="badge badge-danger p-2">Pelanggaran: {{ $violationPercentage }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Siswa dengan prestasi terbanyak -->
                <div class="student-card achievement">
                    <div class="student-card-header">
                        <div>Siswa dengan prestasi terbanyak</div>
                        <a href="{{ route('achievements.all_students') }}">Lihat detail >></a>
                    </div>
                    <ul class="student-list">
                        @foreach ($topAchievementStudents as $item)
                            <li class="student-item">
                                <div class="student-name">{{ $item->name }}</div>
                                <div class="student-class">{{ $item->class_name }}</div>
                                <div class="student-points">
                                    <span class="point-number">{{ $item->total_point }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Siswa dengan pelanggaran terbanyak -->
                <div class="student-card">
                    <div class="student-card-header">
                        <div>Siswa dengan Pelanggaran terbanyak</div>
                        <a href="{{ route('violations.all_students') }}">Lihat detail >></a>
                    </div>
                    <ul class="student-list">
                        @foreach ($topViolationStudents as $item)
                            <li class="student-item">
                                <div class="student-name">{{ $item->name }}</div>
                                <div class="student-class">{{ $item->class_name }}</div>
                                <div class="student-points">
                                    <span class="point-number">{{ $item->total_point }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
@endsection


@if (Auth::user()->hasRole('Admin Sekolah'))
    @push('js')
        {{-- Chart.js dan Plugin --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
        <script>
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            const attendanceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chart->pluck('class')) !!},
                    datasets: [{
                        label: 'Jumlah Presensi',
                        data: {!! json_encode($chart->pluck('total_present')) !!},
                        backgroundColor: '#2c3e50',
                        borderRadius: 8,
                        barThickness: 14,
                        categoryPercentage: 0.8,
                        barPercentage: 0.9
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.parsed.x} siswa`;
                                }
                            }
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            color: '#000',
                            font: {
                                weight: 'bold'
                            },
                            formatter: Math.round
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 30,
                            ticks: {
                                stepSize: 5
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 13
                                }
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        </script>
    @endpush
@endif
@if (Auth::user()->hasRole('Siswa'))
    @push('js')
        {{-- FontAwesome --}}
        <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

        {{-- Chart.js --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Pie Chart
            const pieCtx = document.getElementById('pieChart').getContext('2d');
            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: ['Prestasi', 'Pelanggaran'],
                    datasets: [{
                        data: [{{ $pieData['prestasi'] }}, {{ $pieData['pelanggaran'] }}],
                        backgroundColor: ['#00c853', '#ff1744'],
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            // Kehadiran Chart
            const hadirCtx = document.getElementById('kehadiranChart').getContext('2d');
            new Chart(hadirCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(
                        array_values(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']),
                    ) !!},
                    datasets: [{
                        label: 'Kehadiran',
                        data: {!! json_encode(array_values($attendance->toArray())) !!},
                        borderColor: '#ffa726',
                        backgroundColor: 'rgba(255,167,38,0.2)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    @endpush
@endif

@if (Auth::user()->hasRole('Guru BK'))
    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Mendapatkan data dari PHP untuk pie chart
                const achievementPercentage = {{ $achievementPercentage }};
                const violationPercentage = {{ $violationPercentage }};

                // Membuat pie chart untuk perbandingan prestasi dan pelanggaran
                const pieCtx = document.getElementById('pointPieChart').getContext('2d');
                new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Prestasi', 'Pelanggaran'],
                        datasets: [{
                            data: [achievementPercentage, violationPercentage],
                            backgroundColor: ['#2ecc71', '#e74c3c'],
                            borderColor: ['#27ae60', '#c0392b'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: {
                                        size: 14,
                                        weight: 'bold'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.label}: ${context.parsed}%`;
                                    }
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true
                        }
                    }
                });
            });
        </script>
    @endpush
@endif
