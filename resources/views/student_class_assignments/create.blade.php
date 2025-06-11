{{-- resources/views/manage-student-class-assignments/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Plotting Siswa Ke Kelas')

@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .class-info-badge {
            font-size: 1rem !important;
            /* Samakan dengan badge selectedCount */
            padding: 0.45em 0.75em;
            /* Sesuaikan padding agar mirip select */
            background-color: #e9ecef;
            color: #003366;
            /* Warna teks biru tua */
            border: 1px solid #ced4da;
            border-radius: 0.3rem;
            /* Samakan dengan select */
            font-weight: 500;
            display: block;
            /* Pastikan block agar lebar penuh */
            text-align: center;
            /* Teks di tengah */
            line-height: 1.5;
            /* Sesuaikan agar tinggi mirip select */
        }

        .control-group .form-control-plaintext.class-info-badge {
            min-width: 100%;
            /* Agar lebar konsisten */
            text-align: left;
            /* Align teks ke kiri */
        }

        body {
            font-family: 'Roboto', sans-serif;
        }

        .table thead th,
        .table tbody td {
            vertical-align: middle;
            text-align: center;
        }

        .table thead th:nth-child(4),
        .table tbody td:nth-child(4) {
            text-align: left;
        }

        .table-sm th,
        .table-sm td {
            padding: 0.3rem;
        }

        .btn-primary {
            background-color: #1777e5;
            border: none;
        }

        .btn-primary:hover {
            background-color: #1266c4;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.2rem 0.4rem;
            margin-left: 2px;
            background-color: #f4f4f4;
            border-radius: 6px;
            border: 1px solid #ddd;
            color: #1777e5 !important;
            font-size: 0.75rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #1777e5 !important;
            color: white !important;
            border: 1px solid #1777e5;
            font-size: 0.75rem;
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 0.5rem;
            display: flex;
            justify-content: end;
            align-items: center;
            font-size: 0.75rem;
        }

        .dataTables_info {
            color: #1777e5;
            font-size: 0.90rem;
            margin-top: 0.5rem;
        }

        .dataTables_length label {
            font-weight: 500;
            color: #1777e5;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }

        .dataTables_length select {
            min-width: 50px;
            margin: 0 0.3rem;
            padding: 0.3rem 0.5rem;
            font-size: 0.85rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fff;
            color: #1777e5;
            appearance: none;
        }

        .dataTables_length select:focus {
            outline: none;
            border-color: #1777e5;
            box-shadow: 0 0 0 0.1rem rgba(23, 119, 229, 0.25);
        }

        #studentsTable tbody tr:nth-child(odd) {
            background-color: #f4f4f4;
        }

        #studentsTable tbody tr:nth-child(even) {
            background-color: #ffffff;
        }

        #studentsTable,
        #studentsTable thead th,
        #studentsTable tbody td {
            font-size: 14px;
            /* Atau 0.9rem, atau nilai lain yang Anda rasa pas */
        }

        #studentsTable thead th,
        #studentsTable tbody td {
            padding: 8px 10px;
            /* Atas/Bawah: 8px, Kiri/Kanan: 10px. Sesuaikan. */
            vertical-align: middle;
            /* Tetap pertahankan ini */
            line-height: 1.4;
            /* Sedikit spasi antar baris jika perlu */
        }

        /* Targetkan sel <td> di kolom 'Pilih'.
       Kita akan menggunakan kelas 'dt-center-flex' yang akan ditambahkan via DataTables. */
        /* Tetap gunakan CSS Flexbox untuk td */
        #studentsTable>tbody>tr>td.dt-center-flex {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            padding: 0.3rem;
            height: 38px;
            /* Anda bisa coba hapus padding ini sementara untuk melihat efeknya */
        }


        /* Pastikan checkbox direset total marginnya dan tidak ada style aneh */
        #studentsTable td.dt-center-flex .form-check-input.row-select {
            margin: 0 !important;
            /* Hapus semua margin */
            padding: 0 !important;
            /* Hapus semua padding jika ada */
            line-height: normal !important;
            /* Reset line-height */
            height: auto;
            /* Biarkan tingginya natural atau sesuaikan jika perlu */
            /* vertical-align: middle; */
            /* Ini seharusnya tidak lagi diperlukan dengan flex align-items:center */
        }

        /* CSS untuk header tetap sama */
        #studentsTable>thead>tr>th.dt-center-flex-header {
            text-align: center !important;
            vertical-align: middle !important;
        }

        .badge-no-class {
            background-color: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .badge-has-class {
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .row-select {
            transform: scale(1.2);
            cursor: pointer;
        }

        .select-all-container {
            background: #f8f9fa;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
        }

        .select-all-container label {
            margin: 0;
            font-weight: 500;
            color: #495057;
            cursor: pointer;
        }

        .select-all-container input[type="checkbox"] {
            margin-right: 8px;
            transform: scale(1.1);
        }

        .confirmation-card {
            background: #f8f9fa;
            /* Warna latar yang sedikit berbeda dari default card */
            border: 1px solid #dee2e6;
            border-radius: 0.75rem;
            /* Rounded corner lebih besar */
            padding: 25px 30px;
            /* Padding lebih lega */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.075);
            margin-top: 25px;
        }

        .confirmation-card h5 {
            color: #003366;
            /* Warna biru tua untuk judul panel */
            font-weight: 600;
        }

        .control-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 10px;
            height: 100%;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .control-label {
            font-weight: 500;
            /* Sedikit lebih tipis dari bold agar tidak terlalu ramai */
            color: #003366;
            /* Warna biru tua */
            font-size: 0.875rem;
            /* Ukuran font label */
            margin-bottom: 0.5rem !important;
            /* Pastikan margin bawah diterapkan */
            text-align: inherit;
            /* Label rata kiri */
        }

        .control-group .form-select {
            min-width: 100%;
            padding: 8px 12px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            font-size: 14px;
            background-color: white;
            color: #495057;
            transition: all 0.3s ease;
        }

        .form-select-md {
            /* Jika Anda menggunakan Bootstrap 5 */
            padding: 0.45rem 0.9rem;
            font-size: 0.95rem;
            border-radius: 0.3rem;
        }

        .control-group .form-select:focus {
            border-color: #183C70;
            box-shadow: 0 0 0 0.2rem rgba(24, 60, 112, 0.25);
            outline: 0;
        }

        .control-group .badge {
            font-size: 14px;
            padding: 10px 16px;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-confirm {
            background: #009cf3;
            border: none;
            padding: 12px 20px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: white;
            width: 100%;
            font-size: 14px;
        }

        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(24, 60, 112, 0.3);
            color: white;
        }

        .btn-confirm:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        #selectedCount.badge {
            min-height: calc(1.5em + 0.75rem + 2px);
            /* Samakan tinggi minimum */
            display: inline-flex;
            align-items: center;
            justify-content: center;

        }

        #selectedCount.bg-success {
            /* Jika ada kondisi bg-success */
            background-color: #198754 !important;
        }

        .info-text {
            font-size: 0.95rem;
            /* Ukuran font untuk info Tahun Akademik & Kelas Tujuan */
            font-weight: 500;
            color: #333;
            /* Warna teks yang jelas */
            padding: 0.375rem 0.75rem;
            /* Padding mirip input field tapi tanpa border */
            background-color: #e9ecef;
            /* Background senada dengan contoh */
            border-radius: 0.3rem;
            display: block;
            /* Agar memenuhi lebar jika diperlukan */
            line-height: 1.5;
            min-height: calc(1.5em + 0.75rem + 2px);
            /* Samakan tinggi dengan input/badge */
            text-align: center;
            /* Tengahkan teks di dalam info-text */
        }


        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .control-group {
                text-align: center;
                /* Semua grup kontrol teksnya ke tengah di mobile */
            }

            .control-label {
                text-align: center;
                /* Label juga ke tengah di mobile */
            }

            .confirmation-card .col-md-2.text-md-start {
                /* Tombol konfirmasi */
                text-align: center !important;
            }

            .confirmation-card .col-md-2.text-md-start .control-group {
                margin-top: 1rem;
                /* Beri jarak atas untuk tombol di mobile */
            }
        }

        /* DataTables sorting arrows */
        .table th.sorting:before,
        .table th.sorting:after,
        .table th.sorting_asc:before,
        .table th.sorting_asc:after,
        .table th.sorting_desc:before,
        .table th.sorting_desc:after {
            color: white !important;
            opacity: 0.8;
        }
    </style>
    <style>
        /* Sky Blue Button Style */
        /* Sky Blue Button Style - Versi Lengkap */
        .btn-confirm {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            border: none;
            color: white !important;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
        }

        .btn-confirm:hover:not(:disabled) {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.4);
            color: white !important;
        }

        .btn-confirm:active,
        .btn-confirm:focus,
        .btn-confirm:visited {
            color: white !important;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        }

        .btn-confirm:disabled {
            background: #e2e8f0 !important;
            color: #94a3b8 !important;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }
    </style>
    <style>
        /* Sky Blue Button Style */
        /* Sky Blue Button Style - Versi Lengkap */
        .select-all-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 1rem;
            /* optional spacing */
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4" style="font-family: 'Roboto', sans-serif">
        <h1 class="mt-4" style="font-weight: bold; font-size: 2rem; color: #183C70;">
            Plotting Siswa
            @if (isset($selectedClass))
                Ke Kelas: {{ $selectedClass->name }} {{ $selectedClass->parallel_name }}
            @endif
        </h1>

        <div class="card shadow-sm mt-4">
            {{-- ... (Bagian tabel siswa tidak banyak berubah) ... --}}
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0" style="font-weight: 600; color: #183C70;">Kelola Data Siswa</h5>
                    @can('create_student')
                        {{-- Pastikan permission ini sesuai --}}
                        <a href="{{ route('manage-classes.index') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-users"></i> Data Seluruh Kelas
                        </a>
                    @endcan
                </div>

                <div class="select-all-container">
                    <label>
                        <input type="checkbox" id="selectAll">
                        Pilih Semua Siswa
                    </label>
                </div>

                <div class="table-responsive rounded">
                    <table id="studentsTable" class="table table-bordered table-sm"
                        style="border-radius: 5px; overflow: hidden; font-size: 0.85rem;">
                        <thead style="background-color: #009cf3; color: white">
                            <tr>
                                <th>No.</th>
                                <th>NISN</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Jenis Kelamin</th>
                                <th>Tahun Masuk</th>
                                <th>Kelas Saat Ini</th> {{-- Label kolom diubah dari 'Kelas' --}}
                                <th>Pilih</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        {{-- ... bagian atas file create.blade.php ... --}}

        {{-- Panel Kontrol di bawah --}}
        <div class="confirmation-card mt-4">
            <div class="text-center mb-4">
                <h5 style="font-weight: 600; color: #183C70;">
                    <i class="fas fa-cogs me-2"></i>Panel Kontrol Penempatan Siswa
                </h5>
            </div>

            {{-- Hidden inputs for IDs remain crucial --}}
            @if (isset($selectedClassId))
                <input type="hidden" id="targetClassIdHidden" value="{{ $selectedClassId }}">
            @endif
            @if (isset($selectedClass) && $selectedClass->academic_year_id)
                <input type="hidden" id="targetAcademicYearIdHidden" value="{{ $selectedClass->academic_year_id }}">
            @endif

            <div class="row g-3 align-items-center justify-content-center">

                {{-- Kolom Siswa Dipilih --}}
                <div class="col-md-3 col-6">
                    <div class="control-group text-center">
                        <label class="control-label d-block mb-2">Siswa Dipilih:</label>
                        <span id="selectedCount" class="badge bg-primary fs-6 px-3 py-2"
                            style="font-size: 1rem !important; background-color: #0056b3 !important;">0 Siswa</span>
                    </div>
                </div>

                {{-- Kolom Informasi Tahun Akademik & Kelas Tujuan (Gabung atau Tata Ulang) --}}
                <div class="col-md-5 col-6">
                    <div class="control-group text-md-start text-center">
                        <label class="control-label d-block mb-1">Tahun Akademik:</label>
                        <p class="info-text mb-2">
                            @if (isset($selectedClass) && $selectedClass->academicYear)
                                {{ $selectedClass->academicYear->start_year }}/{{ $selectedClass->academicYear->end_year }}
                                - Semester
                                {{ $selectedClass->academicYear->semester == 1 ? 'Ganjil' : ($selectedClass->academicYear->semester == 0 ? 'Ganjil' : 'Genap') }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </p>
                    </div>
                    <div class="control-group text-md-start text-center mt-md-0">
                        <label class="control-label d-block mb-1">Kelas Tujuan:</label>
                        <p class="info-text mb-0">
                            @if (isset($selectedClass))
                                {{ $selectedClass->name }} {{ $selectedClass->parallel_name }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </p>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="control-group">
                        <label class="control-label">Tahun Akademik:</label>
                        <select id="targetYear" name="academic_year_id" class="form-select" required>
                            <option value="">-- Pilih Tahun --</option>
                            @foreach ($academicYears as $ay)
                                <option value="{{ $ay->id }}">{{ $ay->start_year }}/{{ $ay->end_year }} - Semester
                                    {{ $ay->semester == 0 ? 'Genap' : 'Ganjil' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Kolom Tombol Konfirmasi --}}
                <div class="col-md-3 col-12 mt-3 mt-md-0 d-flex align-items-end"> {{-- Flex align-items-end untuk tombol --}}
                    <div class="control-group w-100 text-center">
                        {{-- Spacer label opsional jika ingin alignment sempurna di mobile, atau hapus jika tidak perlu --}}
                        {{-- <label class="control-label d-block mb-2 d-md-none">&nbsp;</label>  --}}
                        <button id="confirmBtn" class="btn btn-confirm btn-md w-100" disabled>
                            <i class="fas fa-check-circle me-2"></i>Konfirmasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('js')
        {{-- ... (JS library Anda yang sudah ada) ... --}}
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                let selectedStudents = new Set();

                // Ambil ID kelas tujuan & tahun akademik jika sudah ditentukan dari server (via hidden input)
                const PRE_SELECTED_CLASS_ID = $('#targetClassIdHidden').val() || null;
                const PRE_SELECTED_CLASS_NAME = PRE_SELECTED_CLASS_ID ?
                    "{{ isset($selectedClass) ? htmlspecialchars($selectedClass->name . ' ' . $selectedClass->parallel_name, ENT_QUOTES) : '' }}" :
                    "";
                // Jika kelas yang dipilih sudah memiliki tahun akademik, gunakan itu.
                let PRE_SELECTED_ACADEMIC_YEAR_ID = $('#targetAcademicYearIdHidden').val() || null;


                // Jika kelas yang dipilih sudah menentukan tahun akademik,
                // pastikan dropdown tahun akademik terpilih dan di-disable.
                if (PRE_SELECTED_ACADEMIC_YEAR_ID) {
                    $('#targetYear').val(PRE_SELECTED_ACADEMIC_YEAR_ID).prop('disabled', true);
                }

                // URL AJAX DataTables tidak perlu diubah, karena method `create` dan `createForClass`
                // di controller Anda sudah menangani request AJAX untuk DataTables siswa.
                // URL untuk DataTables disesuaikan dengan route yang memanggil view ini.
                let ajaxUrl;
                @if (isset($selectedClassId))
                    // Jika halaman ini dibuka untuk kelas tertentu (dari createForClass)
                    ajaxUrl = '{{ route('manage-student-class-assignments.create-for-class', $selectedClassId) }}';
                @else
                    // Jika halaman ini dibuka secara umum (dari create biasa)
                    ajaxUrl = '{{ route('manage-student-class-assignments.create') }}';
                @endif

                const table = $('#studentsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    // ... (konfigurasi DataTables Anda yang lain) ...
                    ajax: ajaxUrl, // Menggunakan URL yang sudah disesuaikan
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            className: 'text-center',
                            orderable: false,
                            searchable: false,
                            width: '60px'
                        },
                        {
                            data: 'nisn',
                            name: 'nisn',
                            className: 'text-center',
                            width: '100px'
                        },
                        {
                            data: 'nis',
                            name: 'nis',
                            className: 'text-center',
                            width: '100px'
                        },
                        {
                            data: 'name',
                            name: 'name',
                            className: 'text-left'
                        },
                        {
                            data: 'gender',
                            name: 'gender',
                            className: 'text-center',
                            width: '120px'
                        },
                        {
                            data: 'enter_year',
                            name: 'enter_year',
                            className: 'text-center',
                            width: '100px'
                        },
                        {
                            data: 'class_name',
                            name: 'class_name',
                            className: 'text-center',
                            orderable: true,
                            searchable: true,
                            width: '150px',
                            render: function(data, type, row) {
                                /* ... render badge kelas ... */
                                if (type === 'display') {
                                    if (!data || data === '-' || data === '' || data === null) {
                                        return '<span class="badge-no-class">Belum Ada Kelas</span>';
                                    }
                                    return '<span class="badge-has-class">' + data + '</span>';
                                }
                                return data || '';
                            }
                        },
                        {
                            data: 'nisn',
                            orderable: false,
                            searchable: false,
                            className: 'dt-center-flex',
                            headerClassName: 'dt-center-flex-header',
                            width: '60px',
                            render: function(data, type, row) {
                                /* ... render checkbox ... */
                                return '<input type="checkbox" class="row-select form-check-input" value="' +
                                    data + '">';
                            }
                            // Untuk sorting dan filtering: return raw value
                            return data || '';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return '<div class="d-flex justify-content-center align-items-center" style="height: 100%;"><input type="checkbox" name="selected_students[]" value="' + row.nisn + '" class="row-select"></div>';
                        }
                    ],
                    order: [
                        [6, 'asc'],
                        [3, 'asc']
                    ], // Default: Kelas A-Z, lalu Nama A-Z
                    language: {
                        /* ... bahasa Anda ... */ },
                    drawCallback: function() {
                        /* ... drawCallback Anda ... */
                        $('.row-select').each(function() {
                            const nisn = $(this).val();
                            $(this).prop('checked', selectedStudents.has(nisn));
                        });
                        updateCount();
                        updateSelectAllState();
                    }
                });

                function updateCount() {
                    /* ... fungsi updateCount Anda ... */
                    const count = selectedStudents.size;
                    $('#selectedCount').text(count + ' Siswa');
                    $('#selectedCount').toggleClass('bg-success', count > 0).toggleClass('bg-primary', count === 0);
                    updateConfirmButton();
                }

                function updateSelectAllState() {
                    /* ... fungsi updateSelectAllState Anda ... */
                    const visibleCheckboxes = $('.row-select');
                    const checkedVisibleCheckboxes = $('.row-select:checked');
                    if (visibleCheckboxes.length === 0) {
                        $('#selectAll').prop({
                            indeterminate: false,
                            checked: false
                        });
                    } else if (checkedVisibleCheckboxes.length === visibleCheckboxes.length) {
                        $('#selectAll').prop({
                            indeterminate: false,
                            checked: true
                        });
                    } else {
                        $('#selectAll').prop({
                            indeterminate: checkedVisibleCheckboxes.length > 0,
                            checked: false
                        });
                    }
                }

                function updateConfirmButton() {
                    const hasSelectedStudents = selectedStudents.size > 0;
                    // Tahun Akademik: Gunakan PRE_SELECTED_ACADEMIC_YEAR_ID jika ada (dari kelas),
                    // atau ambil dari dropdown #targetYear jika tidak ditentukan oleh kelas.
                    const targetYearValue = PRE_SELECTED_ACADEMIC_YEAR_ID || $('#targetYear').val();
                    const hasTargetYear = targetYearValue !== '';

                    // Kelas Tujuan: Jika PRE_SELECTED_CLASS_ID ada, berarti kelas sudah ditentukan.
                    // Jika tidak, ambil dari dropdown #targetClass (skenario lama).
                    const hasTargetClass = PRE_SELECTED_CLASS_ID ? true : ($('#targetClass').val() !== '');

                    const enableButton = hasSelectedStudents && hasTargetYear && hasTargetClass;
                    $('#confirmBtn').prop('disabled', !enableButton);
                }

                // Event handlers (checkbox siswa, selectAll) tidak banyak berubah
                $('#studentsTable tbody').on('change', '.row-select', function() {
                    /* ... event handler Anda ... */
                    const nisn = $(this).val();
                    $(this).is(':checked') ? selectedStudents.add(nisn) : selectedStudents.delete(nisn);
                    updateCount();
                    updateSelectAllState();
                });

                $('#selectAll').change(function() {
                    /* ... event handler Anda ... */
                    const isChecked = $(this).is(':checked');
                    $('.row-select').each(function() {
                        const nisn = $(this).val();
                        $(this).prop('checked', isChecked);
                        isChecked ? selectedStudents.add(nisn) : selectedStudents.delete(nisn);
                    });
                    updateCount();
                });

                // Update tombol konfirmasi jika dropdown TAHUN AKADEMIK berubah
                // (hanya jika dropdown tersebut tidak di-disable).
                // Dropdown KELAS TUJUAN hanya relevan jika tidak ada PRE_SELECTED_CLASS_ID.
                $('#targetYear').change(function() {
                    if (!$(this).is(':disabled')) { // Hanya jika tidak di-disable
                        updateConfirmButton();
                    }
                });
                if (!PRE_SELECTED_CLASS_ID) { // Hanya aktifkan event listener ini jika kelas belum dipilih
                    $('#targetClass').change(updateConfirmButton);
                }


                $('#confirmBtn').click(function() {
                    const nisns = Array.from(selectedStudents);
                    // Ambil class_id dari PRE_SELECTED_CLASS_ID jika ada, jika tidak, ambil dari dropdown #targetClass.
                    const kelasId = PRE_SELECTED_CLASS_ID || $('#targetClass').val();
                    // Ambil academic_year_id dari PRE_SELECTED_ACADEMIC_YEAR_ID jika ada, jika tidak, ambil dari dropdown #targetYear.
                    const tahunAkademikId = PRE_SELECTED_ACADEMIC_YEAR_ID || $('#targetYear').val();

                    if (!nisns.length) {
                        /* ... validasi nisns ... */
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Pilih minimal satu siswa untuk dipindahkan!',
                            confirmButtonColor: '#183C70'
                        });
                        return;
                    }
                    if (!tahunAkademikId) {
                        /* ... validasi tahun akademik ... */
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Pilih tahun akademik terlebih dahulu!',
                            confirmButtonColor: '#183C70'
                        });
                        return;
                    }
                    if (!kelasId) {
                        /* ... validasi kelas (seharusnya tidak terjadi jika PRE_SELECTED_CLASS_ID ada) ... */
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Pilih kelas tujuan terlebih dahulu!',
                            confirmButtonColor: '#183C70'
                        });
                        return;
                    }

                    // Ambil nama kelas untuk pesan konfirmasi
                    const selectedClassNameForAlert = PRE_SELECTED_CLASS_ID ? PRE_SELECTED_CLASS_NAME : $(
                        '#targetClass option:selected').text();
                    // Ambil teks tahun akademik untuk pesan konfirmasi
                    let selectedYearTextForAlert = "";
                    if (PRE_SELECTED_ACADEMIC_YEAR_ID && $('#targetYear').is(':disabled')) {
                        // Jika T.A. sudah ditentukan oleh kelas, ambil teksnya dari opsi yang terpilih (yang sudah kita set)
                        selectedYearTextForAlert = $('#targetYear option:selected').text();
                    } else {
                        // Jika T.A. dipilih manual, ambil dari opsi yang terpilih
                        selectedYearTextForAlert = $('#targetYear option:selected').text();
                    }


                    Swal.fire({
                        /* ... SweetAlert konfirmasi Anda ... */
                        title: 'Konfirmasi Penempatan',
                        html: `
                    <div style="text-align: left; padding: 10px;">
                        <p><strong>Jumlah Siswa:</strong> ${nisns.length} siswa</p>
                        <p><strong>Tahun Akademik:</strong> ${selectedYearTextForAlert}</p>
                        <p><strong>Kelas Tujuan:</strong> ${selectedClassNameForAlert}</p>
                        <hr>
                        <p style="color: #dc3545; font-weight: 600;">Apakah Anda yakin ingin melanjutkan?</p>
                    </div>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#183C70',
                        cancelButtonColor: '#dc3545',
                        confirmButtonText: 'Ya, Pindahkan!',
                        cancelButtonText: 'Batal',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return $.post(
                                '{{ route('manage-student-class-assignments.store') }}', {
                                    nisns: nisns,
                                    academic_year_id: tahunAkademikId, // Kirim tahunAkademikId yang benar
                                    class_id: kelasId, // Kirim kelasId yang benar
                                    _token: '{{ csrf_token() }}' // Jangan lupa CSRF token
                                });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        /* ... SweetAlert .then() Anda ... */
                        if (result.isConfirmed) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                html: `
                            <div style="text-align: center;">
                                <p>${result.value.message || 'Siswa berhasil dipindahkan ke kelas baru.'}</p>
                                <p style="color: #28a745; font-weight: 600;">${nisns.length} siswa telah dipindahkan ke ${selectedClassNameForAlert}</p>
                            </div>`,
                                confirmButtonColor: '#183C70',
                                timer: 4000,
                                timerProgressBar: true
                            });

                            selectedStudents.clear();
                            table.ajax.reload(null, false); // false agar tidak reset ke page pertama
                            $('#selectAll').prop('checked', false);

                            // Reset tahun akademik hanya jika tidak di-disable (artinya tidak ditentukan oleh kelas)
                            if (!$('#targetYear').is(':disabled')) {
                                $('#targetYear').val('');
                            }
                            // Dropdown kelas tujuan tidak perlu direset jika memang sudah dipilih dari awal (PRE_SELECTED_CLASS_ID ada)
                            if (!PRE_SELECTED_CLASS_ID) {
                                $('#targetClass').val('');
                            }
                            updateCount(); // Ini akan memanggil updateConfirmButton juga
                        }
                    }).catch((error) => {
                        /* ... SweetAlert .catch() Anda ... */
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: error.responseJSON?.message ||
                                'Gagal memindahkan siswa. Silakan coba lagi.',
                            confirmButtonColor: '#183C70'
                        });
                    });
                });

                // Inisialisasi awal
                updateCount(); // Ini akan memanggil updateConfirmButton juga
            });
        </script>
    @endpush
