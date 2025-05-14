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

                {{-- Kartu Poin --}}
                <div class="row text-center mb-4">
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">PRESTASI</h5>
                                <h2>95</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5 class="card-title">PELANGGARAN</h5>
                                <h2>20</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">TOTAL POIN</h5>
                                <h2>75</h2>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('kehadiranChart').getContext('2d');
        const kehadiranChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
                datasets: [{
                    label: 'Minggu Ini',
                    data: [20, 40, 60, 80, 70],
                    borderColor: 'orange',
                    backgroundColor: 'rgba(255,165,0,0.2)',
                    tension: 0.4
                }, {
                    label: 'Minggu Lalu',
                    data: [30, 50, 70, 90, 60],
                    borderColor: 'red',
                    backgroundColor: 'rgba(255,99,132,0.2)',
                    tension: 0.4
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

        $('.toast').toast('show');
    </script>
@endpush
