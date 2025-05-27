<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Jadwal Kelas</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px; 
            margin: 20px;
        }
        h2, h3 { 
            text-align: center; 
            margin: 10px 0; 
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px;
            border: none;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            border: 1px solid #333; 
            padding: 8px; 
            text-align: center; 
            vertical-align: middle;
        }
        th { 
            background-color: #f0f0f0; 
            font-weight: bold;
        }
        .day-cell {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .break-cell {
            background-color: #fff3cd;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h2>JADWAL PELAJARAN</h2>
    <h3>{{ $class->name ?? 'N/A' }} - {{ $class->parallel_name ?? 'N/A' }}</h3>
    
    @if($schedule && $schedule->assignment && $schedule->assignment->academicYear)
    <h3>TAHUN AKADEMIK {{ $schedule->assignment->academicYear->start_year }}/{{ $schedule->assignment->academicYear->end_year }}</h3>
    @else
    <h3>TAHUN AKADEMIK -</h3>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Hari</th>
                <th style="width: 15%;">Jam Ke</th>
                <th style="width: 20%;">Waktu</th>
                <th style="width: 30%;">Mata Pelajaran</th>
                <th style="width: 20%;">Guru Pengampu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedulesPerDay as $day => $items)
                @if(count($items) > 0)
                    @foreach($items as $index => $item)
                        <tr>
                            @if($index == 0)
                                <td rowspan="{{ count($items) }}" class="day-cell">{{ $day }}</td>
                            @endif
                            
                            <td>
                                @if($item['start_hour_slot'] == $item['end_hour_slot'])
                                    {{ $item['start_hour_slot'] }}
                                @else
                                    {{ $item['start_hour_slot'] }} - {{ $item['end_hour_slot'] }}
                                @endif
                            </td>
                            
                            <td>
                                @if(isset($item['start_time']) && isset($item['end_time']))
                                    {{ substr($item['start_time'], 0, 5) }} - {{ substr($item['end_time'], 0, 5) }}
                                @else
                                    -
                                @endif
                            </td>
                            
                            <td class="{{ $item['session_type'] == 'Jam Istirahat' ? 'break-cell' : '' }}">
                                {{ $item['session_type'] == 'Jam Istirahat' ? 'ISTIRAHAT' : ($item['subject_name'] ?? 'N/A') }}
                            </td>
                            
                            <td>
                                {{ $item['session_type'] == 'Jam Istirahat' ? '' : ($item['teacher_name'] ?? 'N/A') }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="day-cell">{{ $day }}</td>
                        <td colspan="4" style="text-align: center; font-style: italic; color: #666;">
                            Tidak ada jadwal
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>