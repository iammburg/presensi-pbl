@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4" style="font-family: 'Roboto', sans-serif">
        <h1 class="mt-4" style="font-weight: bold; font-size: 2rem; color: #183C70;">Manajemen Pengguna</h1>

        <div class="card shadow-sm mt-5">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0" style="font-weight: 600; color: #183C70;">Data Pengguna</h5>
                    <a href="{{ route('manage-user.create') }}" class="btn"
                        style="width: 190px; background-color: #1777e5; color: white">
                        <i class="fas fa-user-plus"></i> Tambah Pengguna
                    </a>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <label style="color: #1777e5; font-weight: normal;">Show
                            <select class="form-select d-inline-block w-auto mx-2" style="color: #1777e5">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50" selected>50</option>
                                <option value="100">100</option>
                            </select>
                            entries
                        </label>
                    </div>
                    <div class="d-flex align-items-center">
                        <label for="search" class="me-3 mb-0"
                            style="margin-right: 10px !important; color: #979797; font-weight: normal;">Search : </label>
                        <input id="search" type="search" class="form-control d-inline-block" aria-label="Search"
                            style="width: 180px; color: #979797; border: 1px solid #979797;">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" style="border-radius: 5px; overflow: hidden">
                        <thead style="background-color: #1777e5; color: white">
                            <tr>
                                <th style="width: 5%">No.</th>
                                <th>Nama Pengguna</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th style="width: 10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>
                                        @foreach ($item->roles->pluck('name') as $role)
                                            {{ $role }}
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-block btn-sm"
                                                style="border: 1px solid #1777e5; color: #1777e5" data-toggle="dropdown">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <div class="dropdown-menu" role="menu">
                                                <a class="dropdown-item"
                                                    href="{{ route('manage-user.edit', $item->id) }}">Edit</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="#">Hapus</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($users->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada pengguna ditemukan.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-3" style="color: #1777e5">
                    <div>
                        Showing 1 to {{ count($users) }} of {{ count($users) }} entries
                    </div>
                    <div>
                        <nav>
                            <ul class="pagination mb-0">
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                <li class="page-item"><a class="page-link" href="#">10</a></li>
                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
