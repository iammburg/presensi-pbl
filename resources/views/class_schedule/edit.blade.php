@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Edit Jadwal Kelas</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right"></ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="card-title m-0">Form Edit Jadwal</h5>
                            <div class="card-tools">
                                <a href="{{ route('manage-schedules.index') }}" class="btn btn-tool">
                                    <i class="fas fa-arrow-alt-circle-left"></i>
                                </a>
                            </div>
                        </div>

                        <div class="card-body px-4 py-3">
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <form method="POST" action="{{ route('manage-schedules.update', $manageSchedule->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <div class="col-auto">
                                        <label for="class_id" class="form-label">Kelas</label>
                                        <select name="class_id" id="class_id" class="form-select form-select-sm" required
                                            style="min-width: 200px;">
                                            <option value="">Pilih</option>
                                            @foreach ($classes as $classItem)
                                                <option value="{{ $classItem->id }}"
                                                    {{ old('class_id', $class->id) == $classItem->id ? 'selected' : '' }}>
                                                    {{ $classItem->name }} - {{ $classItem->parallel_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                @php $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']; @endphp

                                <div class="container-fluid px-2">
                                    @foreach ($days as $day)
                                        <div class="card mb-3 border-light border">
                                            <div class="card-header fw-semibold py-2 px-3 text-white"
                                                style="background-color: #1D3F72;">{{ $day }}</div>
                                            <div class="card-body p-2 schedule-body" id="schedule-{{ $day }}">
                                                {{-- Load existing schedules --}}
                                                @if (isset($scheduleData[$day]))
                                                    @foreach ($scheduleData[$day] as $index => $schedule)
                                                        <div class="row g-2 align-items-end mb-2 schedule-row">
                                                            <div class="col-md-2">
                                                                <label class="form-label small mb-1">Tipe Sesi</label>
                                                                <select
                                                                    name="schedules[{{ $day }}][{{ $index }}][session_type]"
                                                                    class="form-select form-select-sm session-type"
                                                                    onchange="filterHours(this)" required>
                                                                    <option value="">-- Pilih --</option>
                                                                    <option value="Jam Pelajaran"
                                                                        {{ $schedule['session_type'] == 'Jam Pelajaran' ? 'selected' : '' }}>
                                                                        Jam Pelajaran</option>
                                                                    <option value="Jam Istirahat"
                                                                        {{ $schedule['session_type'] == 'Jam Istirahat' ? 'selected' : '' }}>
                                                                        Jam Istirahat</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-2">
                                                                <label class="form-label small mb-1">Jam Mulai</label>
                                                                <select
                                                                    name="schedules[{{ $day }}][{{ $index }}][start_hour_id]"
                                                                    class="form-select form-select-sm hour-select-start"
                                                                    onchange="updateEndHours(this); toggleSubjectTeacher(this)"
                                                                    data-day="{{ $day }}"
                                                                    data-selected="{{ $schedule['start_hour_id'] }}" required>
                                                                    <option value="">-- Pilih Jam Mulai --</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-2">
                                                                <label class="form-label small mb-1">Jam Selesai</label>
                                                                <select
                                                                    name="schedules[{{ $day }}][{{ $index }}][end_hour_id]"
                                                                    class="form-select form-select-sm hour-select-end"
                                                                    data-day="{{ $day }}"
                                                                    data-selected="{{ $schedule['end_hour_id'] }}" required>
                                                                    <option value="">-- Pilih Jam Selesai --</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-5 assignment-container"
                                                                style="{{ $schedule['session_type'] == 'Jam Istirahat' ? 'display: none;' : '' }}">
                                                                <label class="form-label small mb-1">Mata Pelajaran & Guru</label>
                                                                <div class="d-flex gap-2 align-items-center">
                                                                    <div class="search-input flex-grow-1">
                                                                        <input type="text"
                                                                            class="form-control form-control-sm assignment-search"
                                                                            placeholder="Ketik untuk mencari..."
                                                                            autocomplete="off"
                                                                            onkeyup="searchAssignment(this)"
                                                                            onclick="showSearchResults(this)"
                                                                            value="{{ isset($schedule['assignment_id']) && $schedule['assignment_id'] ? $teachingAssignments->where('id', $schedule['assignment_id'])->first()['subject_name'] ?? '' . ' - ' . $teachingAssignments->where('id', $schedule['assignment_id'])->first()['teacher_name'] ?? '' : '' }}">
                                                                        <input type="hidden"
                                                                            name="schedules[{{ $day }}][{{ $index }}][assignment_id]"
                                                                            class="assignment-id"
                                                                            value="{{ $schedule['assignment_id'] ?? '' }}">
                                                                        <div class="search-results"></div>
                                                                    </div>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-danger"
                                                                        onclick="this.closest('.schedule-row').remove()">
                                                                        Hapus
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-1 text-center assignment-hidden"
                                                                style="{{ $schedule['session_type'] == 'Jam Pelajaran' ? 'display: none;' : '' }}">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    onclick="this.closest('.schedule-row').remove()">
                                                                    Hapus
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="card-footer bg-white text-end py-2 px-3">
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                    onclick="addScheduleRow('{{ $day }}')">
                                                    <i class="fas fa-plus"></i> Tambah
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-block btn-flat text-white" style="background-color: #1D3F72">
                                            <i class="fa fa-save"></i> Update Jadwal
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .schedule-row {
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin-bottom: 0.5rem !important;
            background-color: transparent;
        }

        .search-input {
            position: relative;
        }

        .search-results {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 0.375rem;
            z-index: 1000;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            top: 100%;
            left: 0;
        }

        .search-result-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.875rem;
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .form-label.small {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.25rem;
        }

        .card-body.schedule-body {
            min-height: 60px;
        }

        .assignment-container .d-flex {
            min-height: 31px;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .gap-2 {
            gap: 0.5rem !important;
        }
    </style>

    <script>
        const subjects = @json($subjects);
        const hoursData = @json($hoursData);
        const teachingAssignments = @json($teachingAssignments);
        const scheduleData = @json($scheduleData);

        function getHoursForDay(day) {
            return day === 'Jumat' ? hoursData.friday : hoursData.weekdays;
        }

        function addScheduleRow(day) {
            const container = document.getElementById('schedule-' + day);
            const index = container.children.length;

            const row = document.createElement('div');
            row.className = 'row g-2 align-items-end mb-2 schedule-row';

            row.innerHTML = `
                <div class="col-md-2">
                    <label class="form-label small mb-1">Tipe Sesi</label>
                    <select
                        name="schedules[${day}][${index}][session_type]"
                        class="form-select form-select-sm session-type"
                        onchange="filterHours(this)"
                        required
                    >
                        <option value="">-- Pilih --</option>
                        <option value="Jam Pelajaran">Jam Pelajaran</option>
                        <option value="Jam Istirahat">Jam Istirahat</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small mb-1">Jam Mulai</label>
                    <select
                        name="schedules[${day}][${index}][start_hour_id]"
                        class="form-select form-select-sm hour-select-start"
                        onchange="updateEndHours(this); toggleSubjectTeacher(this)"
                        data-day="${day}"
                        required
                    >
                        <option value="">-- Pilih Jam Mulai --</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small mb-1">Jam Selesai</label>
                    <select
                        name="schedules[${day}][${index}][end_hour_id]"
                        class="form-select form-select-sm hour-select-end"
                        data-day="${day}"
                        required
                    >
                        <option value="">-- Pilih Jam Selesai --</option>
                    </select>
                </div>

                <div class="col-md-5 assignment-container">
                    <label class="form-label small mb-1">Mata Pelajaran & Guru</label>
                    <div class="d-flex gap-2 align-items-center">
                        <div class="search-input flex-grow-1">
                            <input type="text"
                                class="form-control form-control-sm assignment-search"
                                placeholder="Ketik untuk mencari..."
                                autocomplete="off"
                                onkeyup="searchAssignment(this)"
                                onclick="showSearchResults(this)">
                            <input type="hidden"
                                name="schedules[${day}][${index}][assignment_id]"
                                class="assignment-id">
                            <div class="search-results"></div>
                        </div>
                        <button type="button"
                            class="btn btn-sm btn-outline-danger"
                            onclick="this.closest('.schedule-row').remove()">
                            Hapus
                        </button>
                    </div>
                </div>

                <div class="col-md-1 text-center assignment-hidden" style="display: none;">
                    <button type="button"
                        class="btn btn-sm btn-outline-danger"
                        onclick="this.closest('.schedule-row').remove()">
                        Hapus
                    </button>
                </div>
            `;

            container.appendChild(row);
        }

        // Assignment search handlers
        function searchAssignment(input) {
            const value = input.value.toLowerCase();
            const resultsBox = input.parentElement.querySelector('.search-results');
            const hiddenInput = input.parentElement.querySelector('.assignment-id');

            resultsBox.innerHTML = '';
            resultsBox.style.display = 'none';

            if (value.length < 2) return;

            const filtered = teachingAssignments.filter(a =>
                a.subject_name.toLowerCase().includes(value) ||
                a.teacher_name.toLowerCase().includes(value)
            );

            if (filtered.length === 0) {
                const div = document.createElement('div');
                div.className = 'search-result-item';
                div.style.color = '#6c757d';
                div.style.fontStyle = 'italic';
                div.textContent = 'Tidak ada hasil ditemukan';
                resultsBox.appendChild(div);
                resultsBox.style.display = 'block';
                return;
            }

            filtered.forEach(item => {
                const div = document.createElement('div');
                div.className = 'search-result-item';
                div.textContent = `${item.subject_name} - ${item.teacher_name}`;
                div.onclick = () => {
                    input.value = div.textContent;
                    hiddenInput.value = item.id;
                    resultsBox.style.display = 'none';
                };
                resultsBox.appendChild(div);
            });

            resultsBox.style.display = 'block';
        }

        function showSearchResults(input) {
            const box = input.parentElement.querySelector('.search-results');
            if (box && box.innerHTML.trim() !== '') {
                box.style.display = 'block';
            }
        }

        // Close search results when clicking outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.search-input')) {
                document.querySelectorAll('.search-results').forEach(el => el.style.display = 'none');
            }
        });

        function populateHourSelect(select, sessionType, day, selectedValue = '') {
            const hours = getHoursForDay(day);
            const filtered = hours.filter(h => h.session_type === sessionType);

            const options = filtered.map(h =>
                `<option value="${h.id}" data-type="${h.session_type}" data-start="${h.start_time}" data-end="${h.end_time}" data-slot="${h.slot_number}" ${selectedValue == h.id ? 'selected' : ''}>Jam ke-${h.slot_number}</option>`
            ).join('');

            select.innerHTML = `<option value="">-- Pilih Jam Mulai --</option>` + options;
        }

        function filterHours(select) {
            const sessionType = select.value;
            const row = select.closest('.schedule-row');
            const day = row.querySelector('.hour-select-start').dataset.day;

            const startSelect = row.querySelector('.hour-select-start');
            const endSelect = row.querySelector('.hour-select-end');

            // Clear previous selections
            startSelect.value = '';
            endSelect.value = '';

            populateHourSelect(startSelect, sessionType, day);
            populateHourSelect(endSelect, sessionType, day);

            toggleSubjectTeacher(startSelect);
        }

        function updateEndHours(startSelect) {
            const row = startSelect.closest('.schedule-row');
            const endSelect = row.querySelector('.hour-select-end');
            const day = startSelect.dataset.day;
            const sessionType = row.querySelector('.session-type').value;

            if (!startSelect.value || !sessionType) return;

            const hours = getHoursForDay(day);
            const startHourId = parseInt(startSelect.value);
            const startHour = hours.find(h => h.id === startHourId);

            if (!startHour) return;

            // Filter hours that are >= start hour and same session type
            const availableEndHours = hours.filter(h =>
                h.session_type === sessionType && h.id >= startHourId
            );

            const options = availableEndHours.map(h =>
                `<option value="${h.id}" data-type="${h.session_type}">Jam ke-${h.slot_number}</option>`
            ).join('');

            endSelect.innerHTML = `<option value="">-- Pilih Jam Selesai --</option>` + options;

            // Auto-select start hour as minimum end hour
            endSelect.value = startHourId;
        }

        function toggleSubjectTeacher(select) {
            const row = select.closest('.schedule-row');
            const sessionTypeSelect = row.querySelector('.session-type');
            const assignmentDiv = row.querySelector('.assignment-container');
            const assignmentHiddenDiv = row.querySelector('.assignment-hidden');
            const assignmentInput = row.querySelector('.assignment-search');
            const assignmentHiddenInput = row.querySelector('.assignment-id');

            const sessionType = sessionTypeSelect.value;
            const isBreak = (sessionType === 'Jam Istirahat');

            if (isBreak) {
                assignmentDiv.style.display = 'none';
                assignmentHiddenDiv.style.display = '';
                assignmentInput.removeAttribute('required');
                assignmentInput.value = '';
                assignmentHiddenInput.value = '';
            } else {
                assignmentDiv.style.display = '';
                assignmentHiddenDiv.style.display = 'none';
                assignmentInput.setAttribute('required', 'required');
            }
        }

        // Initialize existing rows
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all existing schedule rows
            document.querySelectorAll('.session-type').forEach(select => {
                const sessionType = select.value;
                if (sessionType) {
                    const row = select.closest('.schedule-row');
                    const day = row.querySelector('.hour-select-start').dataset.day;
                    const startSelect = row.querySelector('.hour-select-start');
                    const endSelect = row.querySelector('.hour-select-end');

                    // Get the current selected values
                    const startValue = startSelect.dataset.selected || '';
                    const endValue = endSelect.dataset.selected || '';

                    // Populate hour selects with current values
                    populateHourSelect(startSelect, sessionType, day, startValue);
                    populateHourSelect(endSelect, sessionType, day, endValue);

                    // Set the selected values
                    if (startValue) startSelect.value = startValue;
                    if (endValue) endSelect.value = endValue;

                    // Initialize subject/teacher visibility
                    toggleSubjectTeacher(startSelect);
                }
            });
        });
    </script>
@endsection