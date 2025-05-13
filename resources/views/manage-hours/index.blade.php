@extends('layouts.app')

@section('title', 'Manajemen Jam')

@section('content')
<div class="container-fluid px-4">
    <h3 class="fw-bold mb-4">Manajemen Jam</h3>

    <div class="card shadow-sm rounded">
        <div class="card-header d-flex align-items-center">
            <h5 class="mb-0 flex-grow-1">Kelola Jam Pelajaran</h5>
            <a href="{{ route('manage-hours.create') }}" class="btn text-white ms-auto" style="background-color: #1777e5;">Tambah Jam</a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="text-center text-white" style="background-color: #009cf3;">
                        <tr>
                            <th>No.</th>
                            <th>Tipe Jam</th>
                            <th>Jam ke-</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($hours as $index => $hour)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ ucfirst($hour->session_type) }}</td>
                                <td>Jam ke-{{ $hour->slot_number }}</td>
                                <td>{{ $hour->start_time }}</td>
                                <td>{{ $hour->end_time }}</td>
                                <td class="text-center">
                                    <div class="dropdown d-flex justify-content-center">
                                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton{{ $hour->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-gear-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $hour->id }}">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('manage-hours.edit', $hour->id) }}">
                                                    <i class="bi bi-pencil-square me-2"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('manage-hours.destroy', $hour->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="dropdown-item text-danger" type="submit">
                                                        <i class="bi bi-trash3-fill me-2"></i> Hapus
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">Tidak ada data jam pelajaran</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>  
        </div>
    </div>
</div>
@endsection
