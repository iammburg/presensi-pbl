@extends('layouts.app')

@section('title', 'Manajemen Jam')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6 text-uppercase">
                    <h4 class="m-0">Manajemen Jam</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid pb-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Kelola Data Jam</h3>
                            <div class="card-tools">
                                <a href="{{ route('manage-hours.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus-circle"></i> Tambah Jam
                                </a>
                            </div>
                        </div>

                        <!-- Tabel Senin - Kamis -->
                        <div class="card-body bg-white mb-4">
                            <h5 class="mb-3 text-primary font-weight-bold">Tabel Jam Hari Senin-Kamis</h5>
                            <div class="table-responsive">
                                <table id="hourTableWeekdays" class="table table-bordered table-striped">
                                    <thead class="bg-tertiary text-white">
                                        <tr>
                                            <th>No</th>
                                            <th>Tipe Jam</th>
                                            <th>Jam ke-</th>
                                            <th>Jam Mulai</th>
                                            <th>Jam Selesai</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tabel Jumat -->
                        <div class="card-body bg-white">
                            <h5 class="mb-3 text-primary font-weight-bold">Tabel Jam Hari Jumat</h5>
                            <div class="table-responsive">
                                <table id="hourTableFriday" class="table table-bordered table-striped">
                                    <thead class="bg-tertiary text-white">
                                        <tr>
                                            <th>No</th>
                                            <th>Tipe Jam</th>
                                            <th>Jam ke-</th>
                                            <th>Jam Mulai</th>
                                            <th>Jam Selesai</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@push('js')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function() {
            bsCustomFileInput.init();

            // DataTable untuk Senin - Kamis
            $('#hourTableWeekdays').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: 'simple_numbers',
                ajax: {
                    url: '{{ route('manage-hours.index') }}',
                    data: {
                        is_friday: false
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'session_type',
                        name: 'session_type'
                    },
                    {
                        data: 'slot_number',
                        name: 'slot_number'
                    },
                    {
                        data: 'start_time',
                        name: 'start_time'
                    },
                    {
                        data: 'end_time',
                        name: 'end_time'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // DataTable untuk Jumat
            $('#hourTableFriday').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pagingType: 'simple_numbers',
                ajax: {
                    url: '{{ route('manage-hours.index') }}',
                    data: {
                        is_friday: true
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'session_type',
                        name: 'session_type'
                    },
                    {
                        data: 'slot_number',
                        name: 'slot_number'
                    },
                    {
                        data: 'start_time',
                        name: 'start_time'
                    },
                    {
                        data: 'end_time',
                        name: 'end_time'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

        });

        function deleteHour(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data jam akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/manage-hours/${id}`,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire('Terhapus!', response.message ?? 'Jam berhasil dihapus.',
                                'success');
                            $('#hourTableWeekdays').DataTable().ajax.reload();
                            $('#hourTableFriday').DataTable().ajax.reload();
                        },
                        error: function() {
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
