<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Jadwal Kelas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin: 20px;
            color: #333;
            line-height: 1.4;
        }

        h2, h3, .academic-year {
            text-align: center;
            margin: 10px 0;
            color: #000;
            font-size: 21px; 
        }

        .header-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .day-title {
            margin-top: 30px;
            margin-bottom: 15px;
            font-weight: bold;
            font-size: 18px; 
            text-align: center;
            background-color: #f8f9fa;
            padding: 12px;
            border: 2px solid #333;
            border-radius: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .table-container {
            overflow-x: auto;
            margin-bottom: 25px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            min-width: 600px; 
            background-color: white;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        th, td {
            border: 1px solid #333;
            padding: 10px 8px;
            text-align: center;
            vertical-align: middle;
            font-size: 15px;
            word-wrap: break-word;
        }

        th {
            background-color: #e9ecef;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .break-cell {
            background-color: #fff3cd;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            color: #856404;
        }

        .subject-cell {
            text-align: center;
            text-transform: uppercase;
            font-size: 15px;
            font-weight: 600;
            color: #2c3e50;
        }

        .teacher-cell {
            text-align: center;
            font-size: 15px;
            color: #34495e;
        }

        .no-schedule {
            font-style: italic;
            color: #666;
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .hour-cell {
            background-color: #f1f3f4;
            font-weight: bold;
            color: #2c3e50;
        }

        .time-cell {
            background-color: #f8f9fa;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }

        @media screen and (max-width: 768px) {
            body {
                margin: 10px;
            }
            
            .day-title {
                font-size: 16px;
                padding: 10px;
            }
            
            th, td {
                padding: 6px 4px;
                font-size: 13px;
            }
            
            .table-container {
                overflow-x: scroll;
            }
        }

        /* Print styles */
        @media print {
            body {
                margin: 0;
                font-size: 12px;
            }
            
            .day-title {
                page-break-before: auto;
                margin-top: 20px;
                font-size: 16px;
            }
            
            .table-container {
                page-break-inside: avoid;
                box-shadow: none;
            }
            
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="header-info" style="display: flex; justify-content: space-between; align-items: center;">
    <h2 style="margin: 0;">JADWAL PELAJARAN</h2>
    <h3 style="margin: 0;">
        KELAS {{ $class->name ?? 'N/A' }}{{ isset($class->parallel_name) ? ' - ' . $class->parallel_name : '' }}
    </h3>
</div>
    <div class="academic-year">
        TAHUN AKADEMIK {{ $schedule->schoolClass->academicYear->start_year }} / {{ $schedule->schoolClass->academicYear->end_year }}
    </div>

    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $day)
        <div class="day-title">{{ $day }}</div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 12%;">Jam Ke</th>
                        <th style="width: 18%;">Waktu</th>
                        <th style="width: 35%;">Mata Pelajaran</th>
                        <th style="width: 35%;">Guru Pengampu</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($schedulesPerDay[$day]) && count($schedulesPerDay[$day]) > 0)
                        @foreach($schedulesPerDay[$day] as $item)
                            @for($hour = $item['start_hour_slot']; $hour <= $item['end_hour_slot']; $hour++)
                                @if($item['session_type'] == 'Jam Istirahat' && $hour == $item['start_hour_slot'])
                                    <tr>
                                        <td class="break-cell">-</td>
                                        <td class="break-cell time-cell">
                                            @php
                                                $hourStartTime = $item['hour_times'][$hour] ?? $item['start_time'];
                                                $hourEndTime = $item['hour_end_times'][$item['end_hour_slot']] ?? $item['end_time'];
                                            @endphp
                                            {{ $hourStartTime && $hourEndTime ? substr($hourStartTime, 0, 5) . ' - ' . substr($hourEndTime, 0, 5) : ($hourStartTime ?? '-') }}
                                        </td>
                                        <td class="break-cell" colspan="2">ISTIRAHAT</td>
                                    </tr>
                                @elseif($item['session_type'] != 'Jam Istirahat')
                                    <tr>
                                        <td class="hour-cell">{{ $hour }}</td>
                                        <td class="time-cell">
                                            @php
                                                $hourStartTime = $item['hour_times'][$hour] ?? $item['start_time'];
                                                $hourEndTime = $item['hour_end_times'][$hour] ?? $item['end_time'];
                                            @endphp
                                            {{ $hourStartTime && $hourEndTime ? substr($hourStartTime, 0, 5) . ' - ' . substr($hourEndTime, 0, 5) : ($hourStartTime ?? '-') }}
                                        </td>
                                        <td class="subject-cell">{{ $item['subject_name'] ?? 'N/A' }}</td>
                                        <td class="teacher-cell">{{ $item['teacher_name'] ?? 'N/A' }}</td>
                                    </tr>
                                @endif
                            @endfor
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="no-schedule">Tidak ada jadwal</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>