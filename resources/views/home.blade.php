@extends('layouts.app')

@section('content')
{{-- Style Khusus Dashboard --}}
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
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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
@endsection

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
                legend: { display: false },
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
