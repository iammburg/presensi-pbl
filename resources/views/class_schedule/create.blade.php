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
        }

        .row.g-2.mb-3.align-items-end .col-md-2,
        .row.g-2.mb-3.align-items-end .col-md-4 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .row.g-2.mb-3.align-items-end .form-label {
            margin-bottom: 0.25rem !important;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .row.g-2.mb-3.align-items-end .form-select {
            width: 100%;
            min-height: 31px;
        }

        .assignment-container {
            width: 100%;
        }

        /* Memperkecil tombol hapus */
        .btn-outline-danger.btn-sm {
            padding: 0.2rem 0.4rem !important;
            font-size: 0.7rem !important;
            line-height: 1 !important;
            white-space: nowrap;
            min-width: auto;
        }

        /* Pastikan tidak ada overflow */
        .row.g-2>* {
            flex: 0 0 auto;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {

            .row.g-2.mb-3.align-items-end .col-md-2,
            .row.g-2.mb-3.align-items-end .col-md-4 {
                margin-bottom: 0.5rem;
            }
        }
    </style>

    {{-- SCRIPT --}}
    <script>
        const subjects = @json($subjects);
        const hoursData = @json($hoursData); // Changed from 'hours' to 'hoursData'
        const teachingAssignments = @json($teachingAssignments);

        // Combine weekdays and friday hours for easier access
        const allHours = [...hoursData.weekdays, ...hoursData.friday];

        function addScheduleRow(day) {
            const container = document.getElementById('schedule-' + day);
            const index = container.children.length;

            const row = document.createElement('div');
            row.className = 'row g-2 mb-3 align-items-end';

            row.innerHTML = `
                <div class="col-md-2">
                    <label class="form-label fw-semibold small mb-1">Tipe Sesi</label>
                    <select name="schedules[${day}][${index}][session_type]"
                            class="form-select form-select-sm session-type"
                            onchange="filterHours(this, '${day}')" required>
                        <option value="">-- Pilih --</option>
                        <option value="Jam Pelajaran">Jam Pelajaran</option>
                        <option value="Jam Istirahat">Jam Istirahat</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold small mb-1">Jam Mulai</label>
                    <select name="schedules[${day}][${index}][start_hour_id]"
                            class="form-select form-select-sm hour-select-start"
                            onchange="updateEndHours(this); toggleSubjectTeacher(this)" required>
                        <option value="">Jam ke-</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold small mb-1">Jam Selesai</label>
                    <select name="schedules[${day}][${index}][end_hour_id]"
                            class="form-select form-select-sm hour-select-end" required>
                        <option value="">Jam ke-</option>
                    </select>
                </div>

                <div class="col-md-4 assignment-container">
                    <label class="form-label fw-semibold small mb-1 assignment-label">Mata Pelajaran & Guru</label>
                    <select name="schedules[${day}][${index}][assignment_id]"
                            class="form-select form-select-sm assignment-select">
                        <option value="">Pilih</option>
                        ${teachingAssignments.map(a => `
                                            <option value="${a.id}">${a.subject_name} - ${a.teacher_name}</option>
                                        `).join('')}
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="button"
                            class="btn btn-outline-danger btn-sm"
                            style="padding: 0.2rem 0.4rem; font-size: 0.7rem; line-height: 1;"
                            onclick="this.closest('.row').remove()">
                        Hapus
                    </button>
                </div>
            `;

            container.appendChild(row);
        }

        function getHoursForDay(day) {
            // Determine which hours to use based on day
            if (['Senin', 'Selasa', 'Rabu', 'Kamis'].includes(day)) {
                return hoursData.weekdays; // hour_id 0-9
            } else if (day === 'Jumat') {
                return hoursData.friday; // hour_id 10-17
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

            // Clear end select when session type changes
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

            // Get the day from the container id
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
            const assignmentLabel = row.querySelector('.assignment-label');
            const assignmentSelect = row.querySelector('.assignment-select');

            const sessionType = sessionTypeSelect.value;
            const isBreak = (sessionType === 'Jam Istirahat');

            if (isBreak) {
                assignmentContainer.style.display = 'none';
                assignmentSelect.disabled = true;
                assignmentSelect.removeAttribute('required');
                assignmentSelect.value = '';
            } else {
                assignmentContainer.style.display = '';
                assignmentSelect.disabled = false;
                assignmentSelect.setAttribute('required', 'required');
            }
        }
    </script>
@endsection
