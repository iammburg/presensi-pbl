@extends('layouts.app')

@section('content')
@if (auth()->user()->hasRole('Siswa'))
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

            {{-- Pemberitahuan --}}
            <div class="alert alert-info">
                <h6><strong>Pemberitahuan Kepada Orang Tua/Wali Murid</strong></h6>
                <p>
                    Dengan hormat,<br>
                    Kami dengan bangga menginformasikan bahwa putra/putri Bapak/Ibu telah meraih pencapaian prestasi
                    yang membanggakan di sekolah.
                    Sebagai bentuk apresiasi, sekolah telah menyiapkan reward yang dapat diambil oleh siswa yang
                    bersangkutan.
                    Silakan menghubungi guru BK untuk pengambilan hadiah.<br>
                    Atas perhatian dan kerja samanya, kami ucapkan terima kasih.
                </p>
            </div>

            {{-- Layout Utama --}}
            <div class="row mb-4">

                {{-- Kolom Kiri --}}
                <div class="col-md-4">
                    <div class="mb-3">
                        <div class="card text-white bg-success">
                            <div class="card-body text-center">
                                <h5 class="fw-bold">
                                    <i class="fas fa-star"></i> PRESTASI
                                </h5>
                                <h1 clas="fw-bold">95</h1>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="card text-white bg-danger">
                            <div class="card-body text-center">
                                <h5 class="fw-bold">
                                    <i class="fas fa-exclamation-circle"></i> PELANGGARAN
                                </h5>
                                <h1 class="fw-bold">20</h1>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body text-center">
                                <h5 class="fw-bold">
                                    <i class="fas fa-star-half-alt"></i> TOTAL POIN
                                </h5>
                                <h1 class="fw-bold">75</h1>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="font-weight-bold">Progress Prestasi</label>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" style="width: 95%;"></div>
                            <div class="progress-bar" style="width: 5%; background-color: #b7f7b7;"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="font-weight-bold">Progress Pelanggaran</label>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-danger" style="width: 20%;"></div>
                            <div class="progress-bar" style="width: 80%; background-color: #f7b7b7;"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="font-weight-bold">Total Poin</label>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-warning" style="width: 75%;"></div>
                            <div class="progress-bar" style="width: 25%; background-color: #ffe8a1;"></div>
                        </div>
                    </div>

                    <div class="card shadow-sm" style="background-color: #fffaf0; border-radius: 15px;">
                        <div class="card-body">
                            <h5 class="font-weight-bold text-secondary">Keterangan</h5>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="text-secondary">Poin Prestasi</span>
                                <span>95</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-secondary">Poin Pelanggaran</span>
                                <span>20</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total Poin</strong>
                                <strong>75</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grafik Performa Kehadiran --}}
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h5 class="m-0">Performa Kehadiran</h5>
                </div>
                <div class="card-body">
                    <canvas id="kehadiranChart"></canvas>
                </div>
            </div>

        </div>
    </div>
@endif
@endsection

@push('js')
    {{-- FontAwesome --}}
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('kehadiranChart').getContext('2d');
        const kehadiranChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                datasets: [
                    {
                        label: 'Minggu Ini',
                        data: [20, 40, 60, 80, 70],
                        borderColor: 'orange',
                        backgroundColor: 'rgba(255,165,0,0.2)',
                        tension: 0.4
                    },
                    {
                        label: 'Minggu Lalu',
                        data: [30, 50, 70, 90, 60],
                        borderColor: 'red',
                        backgroundColor: 'rgba(255,99,132,0.2)',
                        tension: 0.4
                    }
                ]
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
