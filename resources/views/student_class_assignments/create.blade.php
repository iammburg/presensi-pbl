{{-- resources/views/manage-student-class-assignments/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Plotting Siswa Ke Kelas')

@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
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
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #dee2e6;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-top: 25px;
        }

        .control-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 10px;
            height: 100%;
            justify-content: space-between;
        }

        .control-label {
            font-weight: 600;
            color: #183C70;
            margin-bottom: 12px;
            font-size: 14px;
            white-space: nowrap;
            min-height: 20px;
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
            font-weight: 600;
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

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .confirmation-card .row {
                gap: 15px;
            }

            .control-group {
                margin-bottom: 15px;
            }

            .control-label {
                font-size: 13px;
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
        <h1 class="mt-4" style="font-weight: bold; font-size: 2rem; color: #183C70;">Plotting Siswa Ke Kelas</h1>

        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0" style="font-weight: 600; color: #183C70;">Kelola Data Siswa</h5>
                    @can('create_student')
                        <a href="{{ route('manage-students.index') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-users"></i> Data Seluruh Siswa
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
                                <th>Kelas</th>
                                <th>Pilih</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        {{-- Panel Kontrol di bawah --}}
        <div class="confirmation-card">
            <div class="text-center mb-4">
                <h5 style="font-weight: 600; color: #183C70;">
                    <i class="fas fa-cog me-2"></i>Panel Kontrol Penempatan Siswa
                </h5>
            </div>

            <div class="row g-3 justify-content-center align-items-end">
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="control-group">
                        <label class="control-label">Siswa Dipilih:</label>
                        <span id="selectedCount" class="badge bg-primary">0 Siswa</span>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="control-group">
                        <label class="control-label">Tindakan:</label>
                        <select id="actionSelect" class="form-select" disabled>
                            <option value="move">Pindah Kelas</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="control-group">
                        <label class="control-label">Tahun Akademik:</label>
                        <select id="targetYear" name="academic_year_id" class="form-select" required>
                            <option value="">-- Pilih Tahun --</option>
                            @foreach ($academicYears as $ay)
                                <option value="{{ $ay->id }}">{{ $ay->start_year }}/{{ $ay->end_year }} - Semester
                                    {{ $ay->semester }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="control-group">
                        <label class="control-label">Kelas Tujuan:</label>
                        <select id="targetClass" class="form-select" required>
                            <option value="">-- Pilih Kelas Tujuan --</option>
                            @foreach ($classes as $cls)
                                <option value="{{ $cls->id }}">
                                    {{ $cls->name }} {{ $cls->parallel_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="control-group">
                        <label class="control-label">&nbsp;</label>
                        <button id="confirmBtn" class="btn btn-confirm" disabled>
                            <i class="fas fa-check-circle me-2"></i>Konfirmasi Penempatan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
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

            // Store selected students across pages
            let selectedStudents = new Set();

            const table = $('#studentsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: 'simple_numbers',
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                ajax: '{{ route('manage-student-class-assignments.create') }}',
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
                            if (type === 'display') {
                                if (!data || data === '-' || data === '' || data === null) {
                                    return '<span class="badge-no-class">Belum Ada Kelas</span>';
                                }
                                return '<span class="badge-has-class">' + data + '</span>';
                            }
                            // Untuk sorting dan filtering: return raw value
                            return data || '';
                        }
                    },
                    {
                        data: 'nisn',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        width: '80px',
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="row-select form-check-input" value="' +
                                data + '">';
                        }
                    }
                ],
                order: [
                    [6, 'asc'],
                    [3, 'asc']
                ], // Default: Kelas A-Z, lalu Nama A-Z
                language: {
                    processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden"></span></div></div>',
                    emptyTable: 'Tidak ada data siswa yang tersedia',
                    zeroRecords: 'Tidak ditemukan data yang sesuai dengan pencarian',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                    infoFiltered: '(difilter dari _MAX_ total data)',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir',
                        next: 'Selanjutnya',
                        previous: 'Sebelumnya'
                    }
                },
                drawCallback: function() {
                    // Restore checkbox state after redraw
                    $('.row-select').each(function() {
                        const nisn = $(this).val();
                        $(this).prop('checked', selectedStudents.has(nisn));
                    });
                    updateCount();
                    updateSelectAllState();
                }
            });

            function updateCount() {
                const count = selectedStudents.size;
                $('#selectedCount').text(count + ' Siswa');

                if (count > 0) {
                    $('#selectedCount').removeClass('bg-primary').addClass('bg-success');
                } else {
                    $('#selectedCount').removeClass('bg-success').addClass('bg-primary');
                }

                // Enable/disable confirm button based on selections and form completion
                updateConfirmButton();
            }

            function updateSelectAllState() {
                const visibleCheckboxes = $('.row-select');
                const checkedVisibleCheckboxes = $('.row-select:checked');

                if (visibleCheckboxes.length === 0) {
                    $('#selectAll').prop('indeterminate', false);
                    $('#selectAll').prop('checked', false);
                } else if (checkedVisibleCheckboxes.length === visibleCheckboxes.length) {
                    $('#selectAll').prop('indeterminate', false);
                    $('#selectAll').prop('checked', true);
                } else if (checkedVisibleCheckboxes.length > 0) {
                    $('#selectAll').prop('indeterminate', true);
                    $('#selectAll').prop('checked', false);
                } else {
                    $('#selectAll').prop('indeterminate', false);
                    $('#selectAll').prop('checked', false);
                }
            }

            function updateConfirmButton() {
                const hasSelectedStudents = selectedStudents.size > 0;
                const hasTargetYear = $('#targetYear').val() !== '';
                const hasTargetClass = $('#targetClass').val() !== '';

                const enableButton = hasSelectedStudents && hasTargetYear && hasTargetClass;
                $('#confirmBtn').prop('disabled', !enableButton);
            }

            // Event handlers
            $('#studentsTable tbody').on('change', '.row-select', function() {
                const nisn = $(this).val();
                if ($(this).is(':checked')) {
                    selectedStudents.add(nisn);
                } else {
                    selectedStudents.delete(nisn);
                }
                updateCount();
                updateSelectAllState();
            });

            $('#selectAll').change(function() {
                const isChecked = $(this).is(':checked');
                $('.row-select').each(function() {
                    const nisn = $(this).val();
                    $(this).prop('checked', isChecked);
                    if (isChecked) {
                        selectedStudents.add(nisn);
                    } else {
                        selectedStudents.delete(nisn);
                    }
                });
                updateCount();
            });

            // Update confirm button when dropdowns change
            $('#targetYear, #targetClass').change(function() {
                updateConfirmButton();
            });

            $('#confirmBtn').click(function() {
                const nisns = Array.from(selectedStudents);
                const kelas = $('#targetClass').val();
                const tahun = $('#targetYear').val();

                if (!nisns.length) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Pilih minimal satu siswa untuk dipindahkan!',
                        confirmButtonColor: '#183C70'
                    });
                    return;
                }

                if (!tahun) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Pilih tahun akademik terlebih dahulu!',
                        confirmButtonColor: '#183C70'
                    });
                    return;
                }

                if (!kelas) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Pilih kelas tujuan terlebih dahulu!',
                        confirmButtonColor: '#183C70'
                    });
                    return;
                }

                // Get selected class name for confirmation
                const selectedClassName = $('#targetClass option:selected').text();
                const selectedYear = $('#targetYear option:selected').text();

                // Confirmation dialog
                Swal.fire({
                    title: 'Konfirmasi Penempatan',
                    html: `
                    <div style="text-align: left; padding: 10px;">
                        <p><strong>Jumlah Siswa:</strong> ${nisns.length} siswa</p>
                        <p><strong>Tahun Akademik:</strong> ${selectedYear}</p>
                        <p><strong>Kelas Tujuan:</strong> ${selectedClassName}</p>
                        <hr>
                        <p style="color: #dc3545; font-weight: 600;">Apakah Anda yakin ingin melanjutkan?</p>
                    </div>
                `,
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
                                academic_year_id: tahun,
                                class_id: kelas
                            });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: `
                            <div style="text-align: center;">
                                <p>${result.value.message || 'Siswa berhasil dipindahkan ke kelas baru.'}</p>
                                <p style="color: #28a745; font-weight: 600;">${nisns.length} siswa telah dipindahkan ke ${selectedClassName}</p>
                            </div>
                        `,
                            confirmButtonColor: '#183C70',
                            timer: 4000,
                            timerProgressBar: true
                        });

                        // Reset form and reload table
                        selectedStudents.clear();
                        table.ajax.reload();
                        $('#selectAll').prop('checked', false);
                        $('#targetYear').val('');
                        $('#targetClass').val('');
                        updateCount();
                    }
                }).catch((error) => {
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

            // Initialize
            updateCount();
            updateConfirmButton();
        });
    </script>
@endpush
