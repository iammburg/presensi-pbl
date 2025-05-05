@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4 class="m-0">Manajemen Data Mata Pelajaran!</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    </ol>
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
                        <div class="w-100 d-flex justify-content-between align-items-center">
                            <h5 class="m-0">Kelola Mata Pelajaran</h5>
                            <a href="{{ route('subject.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Tambah Data
                            </a>
                        </div>
                    </div>
                        <div class="card-body">
                        <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead style="background-color: #1777e5; color: white;">
                                        <tr>
                                            <th>No.</th>
                                            <th>Kode Mata Pelajaran</th>
                                            <th>Nama Mata Pelajaran</th>
                                            <!-- <th>Nama Guru Pengampu</th> -->
                                            <th>Deskripsi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($subjects as $subject)
                                    <tr>
                                        <td>{{ $loop->iteration + ($subjects->currentPage() - 1) * $subjects->perPage() }}</td>
                                        <td>{{ $subject->code }}</td>
                                        <td>{{ $subject->name }}</td>
                                        <td>{{ $subject->description ?? 'Opsional' }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                                    Aksi
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('subject.edit', $subject->id) }}">Edit</a>
                                                    <form action="{{ route('subject.destroy', $subject->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="dropdown-item text-danger" type="submit">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        Menampilkan {{ $subjects->firstItem() }} sampai {{ $subjects->lastItem() }} dari total {{ $subjects->total() }} data
                                    </div>
                                    <div>
                                        {{ $subjects->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $('.toast').toast('show')
    </script>
@endpush