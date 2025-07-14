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
                padding: 20px;
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
                width: 50px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
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
                            SISWA AKTIF<br>
                            <span style="font-size: 24px;">{{ $activeStudents }}</span>
                        </div>
                    </div>
                    <div class="summary-card bg-success">
                        <div class="summary-icon"><i class="fas fa-check"></i></div>
                        <div>
                            HADIR<br>
                            <span style="font-size: 24px;">{{ $present }}</span>
                        </div>
                    </div>
                    <div class="summary-card bg-danger">
                        <div class="summary-icon"><i class="fas fa-times"></i></div>
                        <div>
                            ALPHA<br>
                            <span style="font-size: 24px;">{{ $absent }}</span>
                        </div>
                    </div>
                    <div class="summary-card bg-warning text-dark">
                        <div class="summary-icon"><i class="fas fa-home"></i></div>
                        <div>
                            IZIN<br>
                            <span style="font-size: 24px;">{{ $excused }}</span>
                        </div>
                    </div>
                </div>

                {{-- Grafik Presensi --}}
                <div class="col-md-9">
                    <div class="card-container">
                        <canvas id="attendanceChart" height="60"></canvas>
                        <div class="legend text-center">
                            <i class="fas fa-square" style="color:#2c3e50; font-size: 14px;"></i>
                            Jumlah Presensi Siswa Setiap Kelas
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
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
                    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
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
                                <a href="{{ route('student.achievements') }}" class="see-detail-link">Lihat Detail &gt;&gt;</a>
                            </div>
                            @foreach($prestasiList as $item)
                                <div class="dashboard-table-row">
                                    <span>{{ $item['name'] }}</span>
                                    <span>{{ $item['point'] }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="dashboard-section">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold">Pelanggaran</span>
                                <a href="{{ route('student.violations') }}" class="see-detail-link">Lihat Detail &gt;&gt;</a>
                            </div>
                            @foreach($pelanggaranList as $item)
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

        @if (Auth::user()->hasRole('Guru BK'))
            <style>
                .dashboard-title {
                    font-size: 24px;
                    font-weight: bold;
                    margin-bottom: 20px;
                }
                .dashboard-section-title {
                    font-size: 20px;
                    font-weight: 600;
                    margin-bottom: 18px;
                    margin-top: 32px;
                }
                .dashboard-table-box {
                    background: #fff8e1;
                    border-radius: 16px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
                    padding: 24px 32px 12px 32px;
                    margin-bottom: 24px;
                }
                .dashboard-table-header, .dashboard-table-row {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    font-size: 16px;
                }
                .dashboard-table-header {
                    font-weight: bold;
                    color: #222;
                    margin-bottom: 8px;
                }
                .dashboard-table-row {
                    margin-bottom: 4px;
                }
                .dashboard-table-row span {
                    min-width: 120px;
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
                <h4 class="dashboard-title">Dashboard Guru BK</h4>
                <div class="dashboard-section-title">Siswa dengan Poin Prestasi Tertinggi</div>
                <div class="dashboard-table-box">
                    <div class="dashboard-table-header">
                        <span>Nama Siswa</span>
                        <span>Kelas</span>
                        <span>Total Poin</span>
                        <span><a href="{{ route('achievements.index') }}" class="see-detail-link">Lihat detail &gt;&gt;</a></span>
                    </div>
                    @foreach($topAchievementStudents as $item)
                        <div class="dashboard-table-row">
                            <span>{{ $item->name }}</span>
                            <span>{{ $item->class_name }}</span>
                            <span>{{ $item->total_point }}</span>
                            <span></span>
                        </div>
                    @endforeach
                </div>
                <div class="dashboard-section-title">Siswa dengan Poin Pelanggaran Tertinggi</div>
                <div class="dashboard-table-box">
                    <div class="dashboard-table-header">
                        <span>Nama Siswa</span>
                        <span>Kelas</span>
                        <span>Total Poin</span>
                        <span><a href="{{ route('violations.index') }}" class="see-detail-link">Lihat detail &gt;&gt;</a></span>
                    </div>
                    @foreach($topViolationStudents as $item)
                        <div class="dashboard-table-row">
                            <span>{{ $item->name }}</span>
                            <span>{{ $item->class_name }}</span>
                            <span>{{ $item->total_point }}</span>
                            <span></span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
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
                        legend: { position: 'bottom' }
                    }
                }
            });
            // Kehadiran Chart
            const hadirCtx = document.getElementById('kehadiranChart').getContext('2d');
            new Chart(hadirCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(array_values(["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"])) !!},
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
                        y: { beginAtZero: true }
                    }
                }
            });
        </script>
    @endpush
@endif
