@extends('layouts.app')
@section('title', 'Buat Jadwal Kelas')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Manajemen Jadwal</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right"></ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h5 class="card-title m-0">Form Buat Jadwal</h5>
                    <div class="card-tools">
                        <a href="{{ route('manage-schedules.index') }}" class="btn btn-tool" title="Kembali">
                            <i class="fas fa-arrow-alt-circle-left"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body px-4 py-3">
                    <form method="POST" action="{{ route('manage-schedules.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <label for="class_id" class="form-label fw-semibold">Kelas</label>
                                <select name="class_id" id="class_id" class="form-select" required
                                    onchange="onClassChange(this)">
                                    <option value="" selected>Pilih Kelas</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}"
                                            {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }} - {{ $class->parallel_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @php
                            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                        @endphp

                        @foreach ($days as $day)
                            <div class="card mb-3 mb-md-4 shadow-sm">
                                <div class="card-header bg-primary text-white py-2 py-md-3">
                                    <h6 class="mb-0 fw-bold">{{ $day }}</h6>
                                </div>
                                <div class="card-body p-2 p-md-3" id="schedule-{{ $day }}">
                                    <div class="text-muted text-center py-3">
                                        <i class="fas fa-calendar-plus fa-lg fa-md-2x mb-2"></i>
                                        <p class="mb-0 small">Belum ada jadwal untuk hari {{ $day }}</p>
                                    </div>
                                </div>
                                <div class="card-footer bg-light text-center text-md-end py-2 px-2 px-md-3">
                                    <button type="button" class="btn btn-primary btn-sm w-100 w-md-auto"
                                        onclick="addScheduleRow('{{ $day }}')">
                                        <i class="fas fa-plus me-1 mr-2"></i>Tambah
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        <div class="text-center mt-3 mt-md-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100 w-md-auto px-4 px-md-5">
                                <i class="fas fa-save me-2 mr-2"></i>Simpan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Ensure Bootstrap styles are applied properly */
        .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 0.75rem center !important;
            background-size: 16px 12px !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 1rem !important;
            line-height: 1.5 !important;
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            height: calc(1.5em + 0.75rem + 2px) !important;
            font-weight: 400 !important;
            color: #212529 !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            word-wrap: normal !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }

        .form-select:focus {
            border-color: #86b7fe !important;
            outline: 0 !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }

        .form-select-sm {
            height: calc(1.5em + 0.5rem + 2px) !important;
            padding: 0.25rem 0.5rem !important;
            font-size: 0.875rem !important;
            border-radius: 0.25rem !important;
        }

        .form-control {
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 1rem !important;
            line-height: 1.5 !important;
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            height: calc(1.5em + 0.75rem + 2px) !important;
            font-weight: 400 !important;
            color: #212529 !important;
            background-color: #fff !important;
            background-clip: padding-box !important;
            appearance: none !important;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out !important;
        }

        .form-control-sm {
            height: calc(1.5em + 0.5rem + 2px) !important;
            padding: 0.25rem 0.5rem !important;
            font-size: 0.875rem !important;
            border-radius: 0.25rem !important;
        }

        .form-control:focus {
            border-color: #86b7fe !important;
            outline: 0 !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }

        .btn {
            border-radius: 0.375rem !important;
            padding: 0.375rem 0.75rem !important;
            font-size: 1rem !important;
            line-height: 1.5 !important;
            display: inline-block !important;
            font-weight: 400 !important;
            text-align: center !important;
            text-decoration: none !important;
            vertical-align: middle !important;
            cursor: pointer !important;
            border: 1px solid transparent !important;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
        }

        .btn-primary {
            color: #fff !important;
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }

        .btn-primary:hover {
            color: #fff !important;
            background-color: #0b5ed7 !important;
            border-color: #0a58ca !important;
        }

        .btn-outline-danger {
            color: #dc3545 !important;
            border-color: #dc3545 !important;
            background-color: transparent !important;
        }

        .btn-outline-danger:hover {
            color: #fff !important;
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
        }

        .search-input {
            position: relative;
            flex-grow: 1;
            width: 100%;
        }

        .search-input .form-control {
            position: relative;
            z-index: 1;
        }

        /* Ensure assignment container has proper positioning */
        .assignment-container {
            position: relative;
            z-index: 1;
            /* Allow dropdown to overflow */
            overflow: visible !important;
        }

        .assignment-container .input-group {
            position: relative;
            z-index: 1;
            /* Allow dropdown to overflow */
            overflow: visible !important;
        }

        .search-results {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            z-index: 999999;
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            max-height: 200px;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            margin-top: 0.125rem;
            top: 100%;
            left: 0;
            right: 0;
            box-sizing: border-box !important;
        }

        /* Only show dropdown when explicitly set to display: block */
        .search-results {
            display: none !important;
        }

        .search-results[style*="display: block"] {
            display: block !important;
            z-index: 999999 !important;
            visibility: visible !important;
            position: absolute !important;
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }

        .search-result-item {
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            border-bottom: 1px solid #dee2e6;
            color: #212529;
            font-size: 0.875rem;
            line-height: 1.5;
            transition: background-color 0.15s ease-in-out;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            z-index: 1050 !important;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-item.text-muted {
            color: #6c757d !important;
            cursor: default;
        }

        .search-result-item.text-muted:hover {
            background-color: transparent;
        }

        .input-group {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            width: 100%;
        }

        .input-group>.form-control {
            position: relative;
            flex: 1 1 auto;
            width: 1%;
            min-width: 0;
        }

        /* Custom schedule row styling */
        .row.g-3.mb-3.p-3.border.rounded.bg-light {
            background-color: #f8f9fa !important;
            border-color: #dee2e6 !important;
            align-items: end !important;
            position: relative !important;
            z-index: 1 !important;
        }

        .row.g-3.mb-3.p-3.border.rounded.bg-light:hover {
            background-color: #e9ecef !important;
        }

        /* Ensure search input container has higher z-index when active */
        .search-input:focus-within {
            z-index: 999999 !important;
            position: relative !important;
        }

        .assignment-container:focus-within {
            z-index: 999999 !important;
            position: relative !important;
        }

        /* Prevent column expansion */
        .row.g-3.mb-3.p-3.border.rounded.bg-light>[class*="col-"] {
            flex-shrink: 0 !important;
            max-width: 100% !important;
            /* Remove overflow hidden to allow dropdown to show */
        }

        /* Ensure form elements don't expand beyond their containers */
        .row.g-3.mb-3.p-3.border.rounded.bg-light .form-select,
        .row.g-3.mb-3.p-3.border.rounded.bg-light .form-control {
            max-width: 100% !important;
            width: 100% !important;
            min-width: 0 !important;
            flex-shrink: 1 !important;
        }

        /* Specific fixes for different column sizes */
        @media (min-width: 768px) {
            .col-md-6 {
                flex: 0 0 auto !important;
                width: 50% !important;
            }
        }

        @media (min-width: 992px) {
            .col-lg-1 {
                flex: 0 0 auto !important;
                width: 8.33333333% !important;
            }

            .col-lg-2 {
                flex: 0 0 auto !important;
                width: 16.66666667% !important;
            }

            .col-lg-3 {
                flex: 0 0 auto !important;
                width: 25% !important;
            }

            .col-lg-5 {
                flex: 0 0 auto !important;
                width: 41.66666667% !important;
            }
        }

        /* Ensure consistent form element heights */
        .form-select-sm,
        .form-control-sm {
            height: calc(1.5em + 0.5rem + 2px) !important;
        }

        /* Prevent layout shift when select value changes */
        .form-select option {
            font-size: inherit !important;
            font-weight: inherit !important;
            color: inherit !important;
            background-color: inherit !important;
        }

        /* Force consistent sizing regardless of content */
        .form-select,
        .form-control {
            box-sizing: border-box !important;
            min-height: calc(1.5em + 0.75rem + 2px) !important;
        }

        .form-select-sm,
        .form-control-sm {
            box-sizing: border-box !important;
            min-height: calc(1.5em + 0.5rem + 2px) !important;
        }

        /* Consistent label spacing */
        .form-label {
            margin-bottom: 0.25rem !important;
            font-weight: 500 !important;
            display: block !important;
        }

        /* Input group alignment */
        .input-group {
            display: flex !important;
            align-items: stretch !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }

        .input-group>.form-control {
            flex: 1 1 auto !important;
            width: 1% !important;
            min-width: 0 !important;
            max-width: 100% !important;
        }

        /* Prevent row expansion */
        .row {
            --bs-gutter-x: 1.5rem !important;
            --bs-gutter-y: 0 !important;
            display: flex !important;
            flex-wrap: wrap !important;
            margin-top: calc(-1 * var(--bs-gutter-y)) !important;
            margin-right: calc(-0.5 * var(--bs-gutter-x)) !important;
            margin-left: calc(-0.5 * var(--bs-gutter-x)) !important;
        }

        .row>* {
            flex-shrink: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            padding-right: calc(var(--bs-gutter-x) * 0.5) !important;
            padding-left: calc(var(--bs-gutter-x) * 0.5) !important;
            margin-top: var(--bs-gutter-y) !important;
        }

        /* Button alignment in column */
        .d-flex.align-items-end {
            min-height: 100% !important;
        }

        /* Card improvements */
        .card {
            border: 1px solid #dee2e6 !important;
            position: relative !important;
            z-index: 1 !important;
        }

        .card-header.bg-primary {
            background-color: #0d6efd !important;
            border-bottom: 1px solid #0d6efd !important;
        }

        /* Ensure dropdown is always on top of cards */
        .card .search-results {
            z-index: 999999 !important;
        }

        /* Override any card z-index when dropdown is active */
        .card:has(.search-results[style*="display: block"]) {
            z-index: 999998 !important;
        }

        /* Form label improvements */
        .form-label {
            margin-bottom: 0.5rem !important;
            font-weight: 500 !important;
        }

        /* Icon improvements */
        .fas {
            font-family: "Font Awesome 5 Free" !important;
            font-weight: 900 !important;
        }

        /* Additional fixes for select dropdown stability */
        .form-select {
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
        }

        .form-select option {
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            max-width: 100% !important;
        }

        /* Ensure container stability */
        .col-12,
        .col-md-6,
        .col-lg-1,
        .col-lg-2,
        .col-lg-3,
        .col-lg-5 {
            position: relative !important;
            /* Remove overflow hidden to allow dropdown to show */
        }

        /* Fix for Bootstrap grid system */
        .container-fluid {
            width: 100% !important;
            padding-right: var(--bs-gutter-x, 0.75rem) !important;
            padding-left: var(--bs-gutter-x, 0.75rem) !important;
            margin-right: auto !important;
            margin-left: auto !important;
        }

        /* Critical fixes for select dropdown expansion */
        .form-select {
            contain: layout !important;
            will-change: auto !important;
        }

        .form-select:focus,
        .form-select:active {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }

        /* Prevent parent container from expanding */
        .row.g-3.mb-3.p-3.border.rounded.bg-light {
            contain: layout !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            position: relative !important;
            z-index: 1 !important;
        }

        /* When dropdown is active, lower the z-index of other rows */
        .row.g-3.mb-3.p-3.border.rounded.bg-light:not(:has(.search-results[style*="display: block"])) {
            z-index: 1 !important;
        }

        /* Boost z-index for row with active dropdown */
        .row.g-3.mb-3.p-3.border.rounded.bg-light:has(.search-results[style*="display: block"]) {
            z-index: 999998 !important;
        }

        /* Force column width constraints */
        .col-lg-2 .form-select {
            max-width: 100% !important;
        }

        .col-lg-5 .input-group {
            max-width: 100% !important;
        }

        /* Remove any potential transforms or transitions that might cause issues */
        .form-select,
        .form-control {
            transform: none !important;
            will-change: auto !important;
        }

        /* Ensure proper box sizing for all elements */
        *,
        *::before,
        *::after {
            box-sizing: border-box !important;
        }

        /* Final fix for select dropdown stability */
        .form-select {
            contain: size layout !important;
            resize: none !important;
            flex-basis: auto !important;
            flex-grow: 0 !important;
            flex-shrink: 1 !important;
        }

        /* Prevent any flexbox issues */
        .row.g-3.mb-3.p-3.border.rounded.bg-light>div {
            flex-grow: 0 !important;
            flex-shrink: 0 !important;
        }

        /* Specific column width fixes */
        .col-lg-2 {
            width: 16.66666667% !important;
            max-width: 16.66666667% !important;
            min-width: 16.66666667% !important;
            flex: 0 0 16.66666667% !important;
        }

        .col-lg-5 {
            width: 41.66666667% !important;
            max-width: 41.66666667% !important;
            min-width: 41.66666667% !important;
            flex: 0 0 41.66666667% !important;
        }

        .col-lg-1 {
            width: 8.33333333% !important;
            max-width: 8.33333333% !important;
            min-width: 8.33333333% !important;
            flex: 0 0 8.33333333% !important;
        }

        @media (max-width: 991.98px) {
            .col-md-6 {
                width: 50% !important;
                max-width: 50% !important;
                min-width: 50% !important;
                flex: 0 0 50% !important;
            }
        }

        @media (max-width: 767.98px) {
            .col-12 {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 100% !important;
                flex: 0 0 100% !important;
            }
        }
    </style>

    <script>
        const subjects = @json($subjects);
        const hoursData = @json($hoursData);
        const teachingAssignments = @json($teachingAssignments);

        let selectedClassId = null;

        // Debug: Log hoursData saat halaman dimuat
        console.log('hoursData loaded:', hoursData);

        function onClassChange(select) {
            selectedClassId = parseInt(select.value);

            // Update all existing search inputs to show new results
            const searchInputs = document.querySelectorAll('.assignment-search');
            searchInputs.forEach(input => {
                // Clear previous selection
                input.value = '';
                const hiddenInput = input.nextElementSibling;
                if (hiddenInput && hiddenInput.classList.contains('assignment-id')) {
                    hiddenInput.value = '';
                }

                // If input is currently focused or has been clicked, update results
                if (input.dataset.clicked === 'true' || document.activeElement === input) {
                    showSearchResults(input);
                }
            });
        }

        function addScheduleRow(day) {
            if (!selectedClassId) {
                alert('Silakan pilih kelas terlebih dahulu sebelum menambah jadwal.');
                return;
            }

            const container = document.getElementById('schedule-' + day);

            // Remove empty state message if this is the first row
            if (container.children.length === 1 && container.querySelector('.text-muted')) {
                container.innerHTML = '';
            }

            const index = container.children.length;

            const row = document.createElement('div');
            row.className = 'row g-3 mb-3 p-3 border rounded bg-light';

            row.innerHTML = `
    <div class="col-12 col-md-6 col-lg-2">
        <label class="form-label fw-semibold small">Tipe Sesi</label>
        <select name="schedules[${day}][${index}][session_type]"
                class="form-select form-select-sm session-type"
                onchange="filterHours(this, '${day}')" required>
            <option value="" selected>Pilih</option>
            <option value="Jam Pelajaran">Jam Pelajaran</option>
            <option value="Jam Istirahat">Jam Istirahat</option>
        </select>
    </div>

    <div class="col-12 col-md-6 col-lg-2">
        <label class="form-label fw-semibold mt-1 small">Jam Mulai</label>
        <select name="schedules[${day}][${index}][start_hour_id]"
                class="form-select form-select-sm hour-select-start"
                onchange="updateEndHours(this); toggleSubjectTeacher(this)" required>
            <option value="" selected>Jam ke-</option>
        </select>
    </div>

    <div class="col-12 col-md-6 col-lg-2">
        <label class="form-label fw-semibold mt-1 small">Jam Selesai</label>
        <select name="schedules[${day}][${index}][end_hour_id]"
                class="form-select form-select-sm hour-select-end" required>
            <option value="" selected>Jam ke-</option>
        </select>
    </div>

    <div class="col-12 col-md-6 col-lg-5 assignment-container">
        <label class="form-label fw-semibold mt-1 small assignment-label">Mata Pelajaran & Guru</label>
        <div class="input-group input-group-sm">
            <div class="search-input position-relative flex-grow-1">
                <input type="text"
                       class="form-control form-control-sm assignment-search"
                       placeholder="Ketik untuk mencari mata pelajaran & guru..."
                       autocomplete="off"
                       onkeyup="searchAssignment(this)"
                       onkeydown="if(event.key === 'Escape') hideSearchResults(this)"
                       onclick="showSearchResults(this)"
                       onfocus="showSearchResults(this)"
                       onblur="setTimeout(() => hideSearchResults(this), 200)">
                <input type="hidden"
                       name="schedules[${day}][${index}][assignment_id]"
                       class="assignment-id">
                <div class="search-results"></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-1 d-flex align-items-end">
        <button type="button"
                class="btn btn-outline-danger btn-sm w-100 mt-3"
                onclick="removeScheduleRow(this, '${day}')"
                title="Hapus">
            <i class="fas fa-trash"></i>
        </button>
    </div>
`;

            container.appendChild(row);
        }

        function searchAssignment(input) {
            const query = input.value.toLowerCase();
            const resultsContainer = input.nextElementSibling.nextElementSibling;

            // Hide dropdown if no query or class not selected
            if (query.length < 1 || !selectedClassId) {
                resultsContainer.style.display = 'none';
                input.dataset.clicked = 'false';
                return;
            }

            // Set dynamic positioning to avoid overlap
            positionDropdown(input, resultsContainer);

            const filteredAssignments = teachingAssignments.filter(assignment => {
                const searchText = `${assignment.subject_name} - ${assignment.teacher_name}`.toLowerCase();
                return (
                    searchText.includes(query) &&
                    assignment.class_id === selectedClassId
                );
            });

            if (filteredAssignments.length > 0) {
                resultsContainer.innerHTML = filteredAssignments.map(assignment =>
                    `<div class="search-result-item"
                      onmousedown="selectAssignment(this, ${assignment.id}, '${assignment.subject_name} - ${assignment.teacher_name}')">
                    ${assignment.subject_name} - ${assignment.teacher_name}
                 </div>`
                ).join('');
                resultsContainer.style.display = 'block';
                resultsContainer.style.zIndex = '999999';
            } else {
                resultsContainer.innerHTML = '<div class="search-result-item text-muted">Tidak ada hasil ditemukan</div>';
                resultsContainer.style.display = 'block';
                resultsContainer.style.zIndex = '999999';
            }
        }

        function selectAssignment(element, assignmentId, assignmentText) {
            const searchInput = element.closest('.search-input').querySelector('.assignment-search');
            const hiddenInput = element.closest('.search-input').querySelector('.assignment-id');
            const resultsContainer = element.closest('.search-input').querySelector('.search-results');

            searchInput.value = assignmentText;
            hiddenInput.value = assignmentId;
            resultsContainer.style.display = 'none';
            searchInput.dataset.clicked = 'false';

            // Remove focus from input to prevent immediate re-opening
            searchInput.blur();
        }

        function hideSearchResults(input) {
            const resultsContainer = input.nextElementSibling.nextElementSibling;
            resultsContainer.style.display = 'none';
            input.dataset.clicked = 'false';

            // Reset z-index of parent row
            const parentRow = input.closest('.row.g-3.mb-3.p-3.border.rounded.bg-light');
            if (parentRow) {
                parentRow.style.zIndex = '1';
            }
        }

        function showSearchResults(input) {
            // Only show if class is selected and input is focused
            if (!selectedClassId) {
                return;
            }

            input.dataset.clicked = 'true';

            // Show all available assignments if no query yet
            if (input.value.length === 0) {
                const resultsContainer = input.nextElementSibling.nextElementSibling;

                // Set dynamic positioning to avoid overlap
                positionDropdown(input, resultsContainer);

                const allAssignments = teachingAssignments.filter(assignment =>
                    assignment.class_id === selectedClassId
                );

                if (allAssignments.length > 0) {
                    resultsContainer.innerHTML = allAssignments.map(assignment =>
                        `<div class="search-result-item"
                          onmousedown="selectAssignment(this, ${assignment.id}, '${assignment.subject_name} - ${assignment.teacher_name}')">
                        ${assignment.subject_name} - ${assignment.teacher_name}
                     </div>`
                    ).join('');
                    resultsContainer.style.display = 'block';
                    resultsContainer.style.zIndex = '999999';
                } else {
                    resultsContainer.innerHTML =
                        '<div class="search-result-item text-muted">Tidak ada mata pelajaran tersedia</div>';
                    resultsContainer.style.display = 'block';
                    resultsContainer.style.zIndex = '999999';
                }
            } else if (input.value.length > 0) {
                searchAssignment(input);
            }
        }

        function positionDropdown(input, dropdown) {
            // Get input position and dimensions
            const inputRect = input.getBoundingClientRect();
            const inputWidth = input.offsetWidth;
            const dropdownHeight = 200; // max-height from CSS
            const viewportHeight = window.innerHeight;

            // Set dropdown width to match input exactly
            dropdown.style.width = inputWidth + 'px';
            dropdown.style.minWidth = inputWidth + 'px';
            dropdown.style.maxWidth = inputWidth + 'px';

            // Check if there's enough space below
            const spaceBelow = viewportHeight - inputRect.bottom;
            const spaceAbove = inputRect.top;

            if (spaceBelow < dropdownHeight && spaceAbove > dropdownHeight) {
                // Show dropdown above input
                dropdown.style.top = 'auto';
                dropdown.style.bottom = '100%';
                dropdown.style.marginTop = '0';
                dropdown.style.marginBottom = '0.125rem';
            } else {
                // Show dropdown below input (default)
                dropdown.style.top = '100%';
                dropdown.style.bottom = 'auto';
                dropdown.style.marginTop = '0.125rem';
                dropdown.style.marginBottom = '0';
            }

            // Ensure highest z-index and absolute positioning
            dropdown.style.zIndex = '999999';
            dropdown.style.position = 'absolute';
            dropdown.style.left = '0';
            dropdown.style.right = '0';
        }

        function getHoursForDay(day) {
            if (['Senin', 'Selasa', 'Rabu', 'Kamis'].includes(day)) {
                return hoursData.weekdays || [];
            } else if (day === 'Jumat') {
                return hoursData.friday || [];
            }
            return [];
        }

        function populateHourSelect(select, sessionType, day) {
            const dayHours = getHoursForDay(day);

            // Check if dayHours is valid
            if (!dayHours || !Array.isArray(dayHours)) {
                console.error('Invalid dayHours data:', dayHours);
                select.innerHTML = '<option value="">Jam ke-</option>';
                return;
            }

            // Filter berdasarkan session type
            const filtered = dayHours.filter(h => h.session_type === sessionType);

            // Create options
            const options = filtered.map(h =>
                `<option value="${h.id}" data-type="${h.session_type}" data-start="${h.start_time}" data-end="${h.end_time}" data-slot="${h.slot_number}">
                Jam ke-${h.slot_number} (${h.start_time} - ${h.end_time})
            </option>`
            ).join('');

            select.innerHTML = `<option value="">Jam ke-</option>` + options;
        }

        function filterHours(select, day) {
            const sessionType = select.value;
            const row = select.closest('.row');

            const startSelect = row.querySelector('.hour-select-start');
            const endSelect = row.querySelector('.hour-select-end');

            // Clear end select first
            endSelect.innerHTML = '<option value="">Jam ke-</option>';

            if (sessionType) {
                // Populate both start and end selects
                populateHourSelect(startSelect, sessionType, day);
                populateHourSelect(endSelect, sessionType, day);
            } else {
                // Clear both selects if no session type selected
                startSelect.innerHTML = '<option value="">Jam ke-</option>';
                endSelect.innerHTML = '<option value="">Jam ke-</option>';
            }

            toggleSubjectTeacher(startSelect);
        }

        function updateEndHours(startSelect) {
            const row = startSelect.closest('.row');
            const endSelect = row.querySelector('.hour-select-end');
            const sessionTypeSelect = row.querySelector('.session-type');

            if (!startSelect.value || !sessionTypeSelect.value) {
                endSelect.innerHTML = '<option value="">Jam ke-</option>';
                return;
            }

            const startHourId = parseInt(startSelect.value);
            const sessionType = sessionTypeSelect.value;
            const container = startSelect.closest('[id^="schedule-"]');
            const day = container.id.replace('schedule-', '');

            const dayHours = getHoursForDay(day);

            // Filter jam yang tersedia untuk jam selesai (>= jam mulai dan session type sama)
            const availableHours = dayHours.filter(h =>
                h.session_type === sessionType &&
                parseInt(h.id) >= startHourId
            );

            const options = availableHours.map(h =>
                `<option value="${h.id}" data-slot="${h.slot_number}">
                Jam ke-${h.slot_number} (${h.start_time} - ${h.end_time})
            </option>`
            ).join('');

            endSelect.innerHTML = `<option value="">Jam ke-</option>` + options;
        }

        function toggleSubjectTeacher(select) {
            const row = select.closest('.row');
            const sessionTypeSelect = row.querySelector('.session-type');
            const assignmentContainer = row.querySelector('.assignment-container');
            const assignmentSearch = row.querySelector('.assignment-search');
            const assignmentId = row.querySelector('.assignment-id');

            const sessionType = sessionTypeSelect.value;
            const isBreak = (sessionType === 'Jam Istirahat');

            if (isBreak) {
                assignmentContainer.style.display = 'none';
                assignmentSearch.removeAttribute('required');
                assignmentSearch.value = '';
                assignmentId.value = '';
            } else {
                assignmentContainer.style.display = '';
                assignmentSearch.setAttribute('required', 'required');
            }
        }

        function removeScheduleRow(button, day) {
            const row = button.closest('.row');
            const container = document.getElementById('schedule-' + day);

            row.remove();

            // If no more rows, show empty state message
            if (container.children.length === 0) {
                container.innerHTML = `
                    <div class="text-muted text-center py-3">
                        <i class="fas fa-calendar-plus fa-lg fa-md-2x mb-2"></i>
                        <p class="mb-0 small">Belum ada jadwal untuk hari ${day}</p>
                    </div>
                `;
            }
        }

        document.addEventListener('click', function(e) {
            // Close dropdown if clicking outside of search input or dropdown
            if (!e.target.closest('.search-input') && !e.target.closest('.search-results')) {
                const allResults = document.querySelectorAll('.search-results');
                allResults.forEach(result => {
                    result.style.display = 'none';
                    // Reset z-index of parent rows
                    const parentRow = result.closest('.row.g-3.mb-3.p-3.border.rounded.bg-light');
                    if (parentRow) {
                        parentRow.style.zIndex = '1';
                    }
                });
                const allSearchInputs = document.querySelectorAll('.assignment-search');
                allSearchInputs.forEach(input => {
                    input.dataset.clicked = 'false';
                });
            }
        });

        // Add event listener for when page loads to ensure dropdowns work
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure all search inputs have proper event handlers
            const searchInputs = document.querySelectorAll('.assignment-search');
            searchInputs.forEach(input => {
                input.dataset.clicked = 'false';

                // Add focus event to boost z-index
                input.addEventListener('focus', function() {
                    const parentRow = this.closest('.row.g-3.mb-3.p-3.border.rounded.bg-light');
                    if (parentRow) {
                        parentRow.style.zIndex = '999998';
                    }
                });

                // Add blur event to reset z-index and hide dropdown
                input.addEventListener('blur', function() {
                    setTimeout(() => {
                        const resultsContainer = this.nextElementSibling.nextElementSibling;
                        // Hide dropdown if not being interacted with
                        if (!resultsContainer.matches(':hover') && document
                            .activeElement !== this) {
                            resultsContainer.style.display = 'none';
                            this.dataset.clicked = 'false';

                            const parentRow = this.closest(
                                '.row.g-3.mb-3.p-3.border.rounded.bg-light');
                            if (parentRow) {
                                parentRow.style.zIndex = '1';
                            }
                        }
                    }, 150);
                });
            });

            // Add window resize listener to reposition visible dropdowns
            window.addEventListener('resize', function() {
                const visibleDropdowns = document.querySelectorAll(
                    '.search-results[style*="display: block"]');
                visibleDropdowns.forEach(dropdown => {
                    const input = dropdown.previousElementSibling.previousElementSibling;
                    if (input && input.classList.contains('assignment-search')) {
                        positionDropdown(input, dropdown);
                    }
                });
            });
        });
    </script>
@endsection
