@extends('layouts.app')

@section('title', 'Manajemen Data Kelas')

@section('content')
<div class="container-fluid px-4">
    <h3 class="fw-bold mb-4">Manajemen Data Kelas</h3>

    <div class="card shadow-sm rounded">
        <div class="card-header d-flex align-items-center">
            <h5 class="mb-0 flex-grow-1">Kelola Data Kelas</h5>
            <a href="{{ route('manage-classes.create') }}" class="btn btn-primary ms-auto">Tambah Data</a>
        </div>



        <div class="card-body">
            <!-- <div class="mb-3 text-end">
                <input type="text" class="form-control w-25 d-inline-block" placeholder="Search...">
            </div> -->

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No.</th>
                            <th>Nama Kelas</th>
                            <th>Paralel</th>
                            <th>Tahun Akademik</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse ($classes as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->parallel_name }}</td>
                                <td>{{ $item->academicYear?->year_label ?? '-' }}</td>
                                <td>
                                    @if ($item->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary btn-sm dropdown-toggle d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-gear-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('manage-classes.edit', $item->id) }}">
                                                    <i class="bi bi-pencil-square me-2"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('manage-classes.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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

                                <!-- <td>
                                    <a href="#" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-gear-fill"></i>
                                    </a>
                                </td> -->
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">Tidak ada data kelas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>  
        </div>
    </div>
</div>
@endsection