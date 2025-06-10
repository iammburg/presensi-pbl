@extends('layouts.app')

@section('title')
Jadwal Mengajar {{ $teacherName ?? 'Guru' }}
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/fullcalendar/main.min.css') }}">
<style>
    .fc {
        font-family: 'Poppins', sans-serif;
    }
    .table-schedule th, .table-schedule td {
        vertical-align: middle;
        text-align: center;
    }
    .filter-label {
        font-weight: 600;
        color: #1D3F72;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Jadwal Mengajar {{ $teacherName ?? 'Guru' }}</h4>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-4">
                <form method="GET" id="filterForm">
                    <label class="filter-label mb-1">Tahun Akademik</label>
                    <select name="academic_year_id" class="form-control" id="academicYearSelect" onchange="document.getElementById('filterForm').submit()">
                        <option value="">-- Pilih Tahun Akademik --</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id', $activeAcademicYearId) == $year->id ? 'selected' : '' }}>
                                {{ $year->start_year }}/{{ $year->end_year }} {{ $year->semester == 1 ? '- Ganjil' : '- Genap' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
        <div class="row mb-4">
            <!-- Filter tanggal dihapus -->
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="card-title m-0">Jadwal Mingguan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-schedule">
                                <thead class="bg-tertiary text-white">
                                    <tr>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Kelas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($weeklySchedules as $item)
                                        <tr>
                                            <td>{{ $item['day'] }}</td>
                                            <td>{{ $item['time'] }}</td>
                                            <td>{{ $item['subject'] }}</td>
                                            <td>{{ $item['class'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Tidak ada jadwal ditemukan.</td>
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
</div>
@endsection

@push('js')
<script src="{{ asset('plugins/fullcalendar/main.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            locale: 'id',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridWeek,timeGridDay'
            },
            events: @json($calendarEvents),
            height: 500,
            slotMinTime: '06:00:00',
            slotMaxTime: '18:00:00',
        });
        calendar.render();
    });
</script>
@endpush