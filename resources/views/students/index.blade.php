@extends('layouts.app')

@section('title', 'Manajemen Data Siswa')

@section('content')
<div class="container-fluid px-4" style="font-family: 'Roboto', sans-serif">
    <h1 class="mt-4" style="font-weight: bold; font-size: 2rem; color: #183C70;">Manajemen Data Siswa</h1>

    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0" style="font-weight: 600; color: #183C70;">Kelola Data Siswa</h5>
                @can('create_student')
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                            id="tambahDataDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-plus"></i> Tambah Data
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="tambahDataDropdown">
                            <a class="dropdown-item" href="{{ route('manage-students.create') }}">
                                <i class="fas fa-keyboard mr-2"></i> Isi Manual
                            </a>
                            <a class="dropdown-item" href="#" id="importExcel">
                                <i class="fas fa-file-excel mr-2"></i> Import Excel
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('manage-students.template') }}">
                                <i class="fas fa-download mr-2"></i> Download Template Excel
                            </a>
                        </div>
                    </div>
                @endcan
            </div>

            <div class="table-responsive rounded">
                <table id="studentsTable" class="table table-bordered table-sm" style="border-radius: 5px; overflow: hidden; font-size: 0.85rem;">
                    <thead style="background-color: #1777e5; color: white">
                        <tr>
                            <th>No.</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>Telepon</th>
                            <th>Nama Orang Tua</th>
                            <th>Telepon Orang Tua</th>
                            <th>Email Orang Tua</th>
                            <th>Jenis Kelamin</th>
                            <th>Tanggal Lahir</th>
                            <th>Tahun Masuk</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('manage-students.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="importExcelModalLabel">Import Data Siswa dari Excel</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="file">Pilih File Excel</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="file" name="file" required accept=".xlsx, .xls">
                        <label class="custom-file-label" for="file">Pilih file</label>
                    </div>
                    <small class="form-text text-muted">File harus dalam format .xlsx atau .xls</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
<style>
    body {
        font-family: 'Roboto', sans-serif;
    }

    .table thead th {
        vertical-align: middle;
        text-align: left;
    }

    .table tbody td {
        vertical-align: middle;
        text-align: left;
    }

    .table-sm th, .table-sm td {
        padding: 0.3rem;
    }

    .btn-primary {
        background-color: #1777e5;
        border: none;
    }

    .btn-primary:hover {
        background-color: #1266c4;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.3rem 0.6rem;
        margin-left: 2px;
        background-color: #f4f4f4;
        border-radius: 6px;
        border: 1px solid #ddd;
        color: #1777e5 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background-color: #1777e5 !important;
        color: white !important;
        border: 1px solid #1777e5;
    }

    .dropdown-menu {
        min-width: 100px;
    }

    .dropdown-menu a {
        font-size: 0.85rem;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(function () {
        bsCustomFileInput.init();

        $('#studentsTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: '{{ route('manage-students.index') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nisn', name: 'nisn' },
                { data: 'name', name: 'name' },
                { data: 'address', name: 'address' },
                { data: 'phone', name: 'phone' },
                { data: 'parent_name', name: 'parent_name' },
                { data: 'parent_phone', name: 'parent_phone' },
                { data: 'parent_email', name: 'parent_email' },
                { data: 'gender', name: 'gender' },
                { data: 'birth_date', name: 'birth_date' },
                { data: 'enter_year', name: 'enter_year' },
                { data: 'role', name: 'role', defaultContent: 'Siswa' },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="actionMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="actionMenu">
                                    <a class="dropdown-item" href="/manage-students/${row.nisn}/edit">Edit</a>
                                    <a class="dropdown-item text-danger" href="#" onclick="deleteStudent('${row.nisn}')">Hapus</a>
                                </div>
                            </div>
                        `;
                    }
                }
            ]
        });

        $('#importExcel').click(function () {
            $('#importExcelModal').modal('show');
        });
    });

    function deleteStudent(nisn) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data siswa akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/manage-students/${nisn}`,
                    type: 'DELETE',
                    data: { "_token": "{{ csrf_token() }}" },
                    success: function (response) {
                        Swal.fire('Terhapus!', response.message, 'success');
                        $('#studentsTable').DataTable().ajax.reload();
                    },
                    error: function () {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                    }
                });
            }
        });
    }

    @if (session('success'))
        toastr.success('{{ session('success') }}');
    @endif

    @if (session('error'))
        toastr.error('{!! session('error') !!}');
    @endif
</script>
@endpush