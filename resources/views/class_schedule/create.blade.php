@extends('layouts.app')

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
                            <div class="col-auto">
                                <label for="class_id" class="form-label">Kelas</label>
                                <select name="class_id" id="class_id" class="form-select form-select-sm" required
                                    style="min-width: 200px;">
                                    <option value="">Pilih</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}"
                                            {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }} - {{ $class->parallel_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @php $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']; @endphp

                        @foreach ($days as $day)
                            <div class="card mb-3 border-light border">
                                <div class="card-header fw-semibold py-2 px-3 text-white" style="background-color: #1D3F72">
                                    {{ $day }}</div>
                                <div class="card-body p-3" id="schedule-{{ $day }}"></div>
                                <div class="card-footer bg-white text-end py-2 px-3">
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="addScheduleRow('{{ $day }}')">+ Tambah</button>
                                </div>
                            </div>
                        @endforeach

                        <button type="submit" class="btn btn-block btn-flat text-white" style="background-color: #1D3F72">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- CSS untuk memperbaiki layout jadwal --}}
    <style>
        /* CSS tambahan untuk memperbaiki layout jadwal */
        .row.g-2.mb-3.align-items-end {
            margin-bottom: 1rem !important;
            align-items: flex-end !important;
        }

        .row.g-2.mb-3.align-items-end .col-md-2,
        .row.g-2.mb-3.align-items-end .col-md-5 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
            display: flex;
            flex-direction: column;
        }

        .row.g-2.mb-3.align-items-end .form-label {
            margin-bottom: 0.25rem !important;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .row.g-2.mb-3.align-items-end .form-select,
        .row.g-2.mb-3.align-items-end .form-control {
            width: 100%;
            min-height: 31px;
            height: 31px;
            border: 1px solid #000000 !important;
        }

        .assignment-container {
            width: 100%;
            position: relative;
        }

        /* Memperbaiki alignment tombol hapus */
        .btn-outline-danger.btn-sm {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.75rem !important;
            line-height: 1.2 !important;
            white-space: nowrap;
            min-width: auto;
            height: 31px; /* Same height as form inputs */
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Search input group styling */
        .search-input-group {
            display: flex;
            align-items: flex-end;
            gap: 0.5rem;
        }

        .search-input {
            position: relative;
            flex: 1;
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #000000;
            border-top: none;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .search-result-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f8f9fa;
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        /* Ensure consistent heights */
        .form-group-consistent {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            min-height: 56px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .row.g-2.mb-3.align-items-end .col-md-2,
            .row.g-2.mb-3.align-items-end .col-md-5 {
                margin-bottom: 0.5rem;
            }
            
            .search-input-group {
                flex-direction: column;
                align-items: stretch;
                gap: 0.25rem;
            }
        }
    </style>

    {{-- SCRIPT --}}
    <script>
        const subjects = @json($subjects);
        const hoursData = @json($hoursData);
        const teachingAssignments = @json($teachingAssignments);

        // Combine weekdays and friday hours for easier access
        const allHours = [...hoursData.weekdays, ...hoursData.friday];

        function addScheduleRow(day) {
            const container = document.getElementById('schedule-' + day);
            const index = container.children.length;

            const row = document.createElement('div');
            row.className = 'row g-2 mb-3 align-items-end';

            row.innerHTML = `
                <div class="col-md-2 form-group-consistent">
                    <label class="form-label fw-semibold small mb-1">Tipe Sesi</label>
                    <select name="schedules[${day}][${index}][session_type]"
                            class="form-select form-select-sm session-type"
                            onchange="filterHours(this, '${day}')" required>
                        <option value="">-- Pilih --</option>
                        <option value="Jam Pelajaran">Jam Pelajaran</option>
                        <option value="Jam Istirahat">Jam Istirahat</option>
                    </select>
                </div>

                <div class="col-md-2 form-group-consistent">
                    <label class="form-label fw-semibold small mb-1">Jam Mulai</label>
                    <select name="schedules[${day}][${index}][start_hour_id]"
                            class="form-select form-select-sm hour-select-start"
                            onchange="updateEndHours(this); toggleSubjectTeacher(this)" required>
                        <option value="">Jam ke-</option>
                    </select>
                </div>

                <div class="col-md-2 form-group-consistent">
                    <label class="form-label fw-semibold small mb-1">Jam Selesai</label>
                    <select name="schedules[${day}][${index}][end_hour_id]"
                            class="form-select form-select-sm hour-select-end" required>
                        <option value="">Jam ke-</option>
                    </select>
                </div>

                <div class="col-md-5 assignment-container form-group-consistent">
                    <label class="form-label fw-semibold small mb-1 assignment-label">Mata Pelajaran & Guru</label>
                    <div class="search-input-group">
                        <div class="search-input">
                            <input type="text" 
                                   class="form-control form-control-sm assignment-search" 
                                   placeholder="Ketik untuk mencari mata pelajaran & guru..."
                                   autocomplete="off"
                                   onkeyup="searchAssignment(this)"
                                   onclick="showSearchResults(this)">
                            <input type="hidden" 
                                   name="schedules[${day}][${index}][assignment_id]" 
                                   class="assignment-id">
                            <div class="search-results"></div>
                        </div>
                        <button type="button"
                                class="btn btn-outline-danger btn-sm ms-2"
                                onclick="this.closest('.row').remove()">
                            Hapus
                        </button>
                    </div>
                </div>
            `;

            container.appendChild(row);
        }

        function searchAssignment(input) {
            const query = input.value.toLowerCase();
            const resultsContainer = input.nextElementSibling.nextElementSibling; // Skip hidden input
            
            // Only show results if user has typed something AND clicked on input
            if (query.length < 1 || !input.dataset.clicked) {
                resultsContainer.style.display = 'none';
                return;
            }

            const filteredAssignments = teachingAssignments.filter(assignment => {
                const searchText = `${assignment.subject_name} - ${assignment.teacher_name}`.toLowerCase();
                return searchText.includes(query);
            });

            if (filteredAssignments.length > 0) {
                resultsContainer.innerHTML = filteredAssignments.map(assignment => 
                    `<div class="search-result-item" 
                          onmousedown="selectAssignment(this, ${assignment.id}, '${assignment.subject_name} - ${assignment.teacher_name}')">
                        ${assignment.subject_name} - ${assignment.teacher_name}
                     </div>`
                ).join('');
                resultsContainer.style.display = 'block';
            } else {
                resultsContainer.innerHTML = '<div class="search-result-item">Tidak ada hasil ditemukan</div>';
                resultsContainer.style.display = 'block';
            }
        }

        function selectAssignment(element, assignmentId, assignmentText) {
            const searchInput = element.closest('.search-input').querySelector('.assignment-search');
            const hiddenInput = element.closest('.search-input').querySelector('.assignment-id');
            const resultsContainer = element.closest('.search-results');
            
            searchInput.value = assignmentText;
            hiddenInput.value = assignmentId;
            resultsContainer.style.display = 'none';
            searchInput.dataset.clicked = 'false'; // Reset click state
        }

        function showSearchResults(input) {
            input.dataset.clicked = 'true'; // Mark that input was clicked
            if (input.value.length > 0) {
                searchAssignment(input);
            }
        }

        function hideSearchResults(input) {
            // Hide results when input loses focus
            setTimeout(() => {
                const resultsContainer = input.nextElementSibling.nextElementSibling;
                resultsContainer.style.display = 'none';
                input.dataset.clicked = 'false'; // Reset click state
            }, 200);
        }

        function getHoursForDay(day) {
            if (['Senin', 'Selasa', 'Rabu', 'Kamis'].includes(day)) {
                return hoursData.weekdays;
            } else if (day === 'Jumat') {
                return hoursData.friday;
            }
            return [];
        }

        function populateHourSelect(select, sessionType, day) {
            const dayHours = getHoursForDay(day);
            const filtered = dayHours.filter(h => h.session_type === sessionType);
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

            endSelect.innerHTML = '<option value="">Jam ke-</option>';

            populateHourSelect(startSelect, sessionType, day);
            populateHourSelect(endSelect, sessionType, day);

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

        // Add event listeners for blur to handle hiding search results
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-input')) {
                const allResults = document.querySelectorAll('.search-results');
                allResults.forEach(result => {
                    result.style.display = 'none';
                });
                // Reset all click states
                const allSearchInputs = document.querySelectorAll('.assignment-search');
                allSearchInputs.forEach(input => {
                    input.dataset.clicked = 'false';
                });
            }
        });
    </script>
@endsection