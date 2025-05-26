@extends('layouts.app')

@section('title')
    Kelola Prestasi
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Kelola Prestasi</h4>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Data Prestasi</h3>
                <div class="card-tools">
                    <a href="{{ route('achievement-management.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Tambah Data
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-tertiary text-white">
                            <tr>
                                <th>No</th>
                                <th>Jenis Prestasi</th>
                                <th>Kategori Prestasi</th>
                                <th>Poin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($achievements as $index => $item)
                                <tr>
                                    <td>{{ $index + $achievements->firstItem() }}</td>
                                    <td>{{ $item->achievement_type }}</td>
                                    <td>{{ $item->achievement_category }}</td>
                                    <td>{{ $item->points }}</td>
                                    <td>
                                        <a href="{{ route('achievement-management.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $item->id }})">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                        <form id="delete-form-{{ $item->id }}" action="{{ route('achievement-management.destroy', $item->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Data tidak tersedia</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $achievements->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#007bff',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endpush
