@extends('layouts.app')

@section('content')
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
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="m-0 text-center">REKAP SISWA KEHADIRAN HARI INI</h5>
                </div>
                <div class="card-body">
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
                        <div class="mb-3">
                            <strong>{{ $item['jam'] }}</strong><br>
                            <span>{{ $item['kelas'] }}</span>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $item['hadir'] }}/{{ $item['total'] }} Siswa
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
