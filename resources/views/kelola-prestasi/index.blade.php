@extends('layouts.app')

@section('content')

<!-- Font Roboto -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Roboto', sans-serif;
    }
    .main-container {
        background-color: #ffffff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .title-prestasi {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 10px;
        text-align: left;
        color: #003366;
    }
    .divider-shadow {
        height: 2px;
        background: linear-gradient(to right, #ccc, #eee, #ccc);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
    }
    .subtitle {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #003366;
    }
    .divider-line {
        height: 1px;
        background-color: #dee2e6;
        margin: 10px 0;
    }
    .description {
        font-size: 18px;
        color: #003366;
        margin-top: 20px;
        margin-bottom: 10px;
        font-weight: 600;
    }
    .control-bar {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 15px;
    }
    .table-responsive {
        margin-bottom: 20px;
        overflow: visible !important;
    }
    .table thead th {
        background-color: #007bff;
        color: white;
        text-align: left;
        vertical-align: middle;
        font-weight: 500;
    }
    .table {
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        /* overflow: hidden; */
    }
    .table td {
        text-align: left;
        vertical-align: middle;
        color: black;
        border: none;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #ffffff;
    }
    .table-striped tbody tr:nth-of-type(even) {
        background-color: #f2f2f2;
    }
    .table tbody tr:hover td {
        background-color: #cce5ff;
    }
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .pagination { margin-bottom: 0; }
    .page-item .page-link {
        color: #007bff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        margin: 0 2px;
    }
    .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
    }
    .page-item .page-link:hover {
        background-color: #0056b3;
        border-color: #0056b3;
        color: white;
    }
    .dropdown-item.text-danger:hover {
        background-color: #f8d7da;
        color: #a71d2a !important;
        z-index: 9999 !important;
    }
    label.form-label { font-weight: normal !important; }
    .btn-action {
        background-color: white;
        border: 2px solid #007bff;
        padding: 6px 10px;
        border-radius: 4px;
    }
    .btn-action i { color: #007bff; }

    /* styling spinner arrows */
    input#entriesInput {
        color: #007bff;
        font-weight: bold;
        text-align: center;
        width: 80px;
        padding: 2px 6px;
    }

    input#entriesInput::-webkit-inner-spin-button,
    input#entriesInput::-webkit-outer-spin-button {
        opacity: 1;
    }

    .dropdown-menu {
        z-index: 1050 !important;
        position: absolute !important;
    }
</style>

<div class="main-container">
    <!-- Sticky Header -->
    <div class="sticky-top bg-white pt-3" style="z-index:99;">
        <div class="title-prestasi">PRESTASI</div>
        <div class="divider-shadow"></div>
        <div class="subtitle">Kelola Prestasi</div>
        <div class="divider-line"></div>
        <div class="description">Data Prestasi</div>
    </div>

    <!-- Tambah, Show Entries, Search -->
    <div class="control-bar mt-3">
        <div class="d-flex justify-content-end">
            <a href="{{ route('prestasi.create') }}" class="btn btn-primary btn-lg px-4 py-2" style="font-size:18px;">
                <i class="fa fa-plus me-1"></i> Tambah Data Prestasi
            </a>
        </div>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <label for="entriesInput" class="form-label mb-0">Show</label>
                <input
    type="number"
    id="entriesInput"
    class="form-control form-control-sm"
    min="10"
    max="100"
    step="1"
    value="{{ request('entries', 10) }}"
    oninput="filterValue(this)"
>
<!-- <script>
    function filterValue(input) {
        const validValues = [25, 50, 100];
        let value = parseInt(input.value);

        // Jika nilai tidak valid, atur ke nilai terdekat yang valid
        if (!validValues.includes(value)) {
            input.value = 25;  // Set to 25 jika nilai tidak valid
        }
    }

    document.getElementById('entriesInput').addEventListener('keydown', function(event) {
        if (event.key === 'ArrowUp') {
            let currentValue = parseInt(this.value);
            const validValues = [25, 50, 100];

            // Cari nilai yang lebih besar dari nilai saat ini
            let nextValue = validValues.find(v => v > currentValue);
            if (nextValue) {
                this.value = nextValue;
            }
        } else if (event.key === 'ArrowDown') {
            let currentValue = parseInt(this.value);
            const validValues = [10, 25, 50, 100];

            // Cari nilai yang lebih kecil dari nilai saat ini
            let prevValue = validValues.reverse().find(v => v < currentValue);
            if (prevValue) {
                this.value = prevValue;
            }
        }
    });
</script> -->
                <label class="form-label mb-0">entries</label>
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="form-label mb-0">Search</label>
                <input
                    type="text"
                    class="form-control form-control-sm"
                    placeholder=""
                    style="width:200px;"
                    value="{{ request('search','') }}"
                    onkeypress="if(event.key==='Enter') { applyFilters(); }"
                >
            </div>
        </div>
    </div>

    <!-- Tabel Prestasi -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Prestasi</th>
                    <th>Kategori Prestasi</th>
                    <th>Poin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($achievementPoints as $key => $item)
                <tr>
                    <td>{{ $achievementPoints->firstItem() + $key }}</td>
                    <td>{{ $item->jenis_prestasi }}</td>
                    <td>{{ $item->kategori_prestasi }}</td>
                    <td>{{ $item->poin }}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn-action" type="button" data-bs-toggle="dropdown">
                                <i class="fa fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('prestasi.edit',$item->id) }}">Edit</a></li>
                                <li>
                                    <form action="{{ route('prestasi.hapus',$item->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
        <div>
            <p class="mb-0" style="color:#003366;">
                Showing {{ $achievementPoints->firstItem() }} to {{ $achievementPoints->lastItem() }} of {{ $achievementPoints->total() }} entries
            </p>
        </div>
        <nav>
            <ul class="pagination">
                @if($achievementPoints->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">Previous</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $achievementPoints->previousPageUrl() }}">Previous</a></li>
                @endif

                @foreach($achievementPoints->getUrlRange(1, $achievementPoints->lastPage()) as $page => $url)
                    <li class="page-item {{ $achievementPoints->currentPage()==$page?'active':'' }}">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach

                @if($achievementPoints->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $achievementPoints->nextPageUrl() }}">Next</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">Next</span></li>
                @endif
            </ul>
        </nav>
    </div>
</div>

<script>
    function applyFilters() {
        const url = new URL(window.location.href);
        const search = document.querySelector('input[placeholder=""]').value;
        const entries = document.getElementById('entriesInput').value;
        url.searchParams.set('entries', entries);
        if(search) url.searchParams.set('search', search);
        else url.searchParams.delete('search');
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('entriesInput').addEventListener('change', applyFilters);
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

@endsection
