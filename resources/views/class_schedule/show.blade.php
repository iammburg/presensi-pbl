@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Detail Jadwal Kelas</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('manage-schedules.index') }}">Manajemen Jadwal</a>
                    </li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Informasi Kelas -->
        <div class="card card-primary card-outline mb-4">
            <div class="card-header">
                <h5 class="card-title m-0">Informasi Jadwal</h5>
                <div class="card-tools">
                    <a href="{{ route('manage-schedules.export-pdf', $schedule->id) }}" class="btn btn-sm btn-danger" target="_blank">
                        <i class="fas fa-file-pdf"></i> Ekspor PDF
                    </a>
                    <a href="{{ route('manage-schedules.edit', $schedule->id) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('manage-schedules.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 120px;">Kelas</th>
                                <td>: {{ $class->name ?? 'N/A' }} - {{ $class->parallel_name ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal per Hari -->
        @foreach($days as $day)
        <div class="card mb-3">
            <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #1D3F72;">
                <h6 class="mb-0 fw-bold">{{ $day }}</h6>
                <small class="badge bg-light text-dark">
                    @php
                        $totalSessions = 0;
                        foreach($schedulesPerDay[$day] as $item) {
                            if($item['start_hour_slot'] == $item['end_hour_slot']) {
                                $totalSessions += 1;
                            } else {
                                $totalSessions += ($item['end_hour_slot'] - $item['start_hour_slot'] + 1);
                            }
                        }
                    @endphp
                    {{ $totalSessions }} Sesi
                </small>
            </div>
            <div class="card-body p-0">
                @if(empty($schedulesPerDay[$day]))
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-calendar-times fa-2x mb-2"></i>
                        <p class="mb-0">Tidak ada jadwal untuk hari {{ $day }}</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;" class="text-center">No</th>
                                    <th style="width: 150px;">Tipe Sesi</th>
                                    <th style="width: 120px;">Jam</th>
                                    <th style="width: 140px;">Waktu</th>
                                    <th>Mata Pelajaran & Guru</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $rowNumber = 1;
                                @endphp
                                @foreach($schedulesPerDay[$day] as $item)
                                    @if($item['start_hour_slot'] == $item['end_hour_slot'])
                                        {{-- Single hour slot --}}
                                        <tr>
                                            <td class="text-center">{{ $rowNumber++ }}</td>
                                            <td>
                                                @if($item['session_type'] == 'Jam Istirahat')
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-coffee"></i> Istirahat
                                                    </span>
                                                @else
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-book"></i> Pelajaran
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold">Jam ke-{{ $item['start_hour_slot'] }}</span>
                                            </td>
                                            <td class="text-muted" style="white-space: nowrap;">
                                                @if($item['start_time'] && $item['end_time'])
                                                    {{ substr($item['start_time'], 0, 5) }}&nbsp;-&nbsp;{{ substr($item['end_time'], 0, 5) }}
                                                @else
                                                    {{ $item['start_time'] ?? '-' }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($item['session_type'] == 'Jam Istirahat')
                                                    <em class="text-muted">
                                                        <i class="fas fa-pause-circle"></i> Waktu Istirahat
                                                    </em>
                                                @else
                                                    <div>
                                                        <strong class="text-primary">
                                                            {{ $item['subject_name'] ?? 'N/A' }}
                                                        </strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-user"></i> 
                                                            {{ $item['teacher_name'] ?? 'N/A' }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @else
                                        {{-- Multiple hour slots - create separate rows for each hour --}}
                                        @for($hour = $item['start_hour_slot']; $hour <= $item['end_hour_slot']; $hour++)
                                            <tr>
                                                <td class="text-center">{{ $rowNumber++ }}</td>
                                                <td>
                                                    @if($item['session_type'] == 'Jam Istirahat')
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-coffee"></i> Istirahat
                                                        </span>
                                                    @else
                                                        <span class="badge bg-primary">
                                                            <i class="fas fa-book"></i> Pelajaran
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="fw-bold">Jam ke-{{ $hour }}</span>
                                                </td>
                                                <td class="text-muted" style="white-space: nowrap;">
                                                    @php
                                                        $hourStartTime = $item['hour_times'][$hour] ?? '-';
                                                        $hourEndTime = $item['hour_end_times'][$hour] ?? '-';
                                                    @endphp
                                                    @if($hourStartTime !== '-' && $hourEndTime !== '-')
                                                        {{ substr($hourStartTime, 0, 5) }}&nbsp;-&nbsp;{{ substr($hourEndTime, 0, 5) }}
                                                    @else
                                                        {{ $hourStartTime }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item['session_type'] == 'Jam Istirahat')
                                                        <em class="text-muted">
                                                            <i class="fas fa-pause-circle"></i> Waktu Istirahat
                                                        </em>
                                                    @else
                                                        <div>
                                                            <strong class="text-primary">
                                                                {{ $item['subject_name'] ?? 'N/A' }}
                                                            </strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="fas fa-user"></i> 
                                                                {{ $item['teacher_name'] ?? 'N/A' }}
                                                            </small>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endfor
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection