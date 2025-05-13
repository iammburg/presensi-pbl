    @extends('layouts.app')

    @section('title', 'Manajemen Data Kelas')

    @section('content')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 text-uppercase">
                        <h4 class="m-0">Manajemen Data Kelas</h4>
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
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Kelola Data Kelas</h3>
                                <div class="card-tools">
                                    @can('create_teacher')
                                        <a href="{{ route('manage-classes.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Tambah Data
                                        </a>
                                    @endcan
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="ClassesTable" class="table table-bordered table-striped"
                                        style="border-radius: 10px; overflow: hidden;">
                                        <thead style="background-color: #009cf3; color: white;">
                                            <tr>
                                                <th>No.</th>
                                                <th>Nama Kelas</th>
                                                <th>Paralel</th>
                                                <th>Tahun Akademik</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
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
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    @endpush

    @push('js')
        <!-- DataTables  & Plugins -->
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <!-- BS Custom File Input -->
        <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
        <!-- Sweet Alert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            $(function() {
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        toastr.error('{{ $error }}');
                    @endforeach
                @endif
            });
        </script>

        <script>
            $(function() {
                bsCustomFileInput.init();

                // resources/views/manage-classes/index.blade.php (inside your <script> tag)
                $('#ClassesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: '{{ route('manage-classes.index') }}',
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'parallel_name',
                            name: 'parallel_name'
                        },
                        // ← here we switch to academic_year
                        {
                            data: 'academic_year',
                            name: 'academic_year'
                        },
                        // ← and here the rendered badge
                        {
                            data: 'status',
                            name: 'is_active',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            });

            function deleteClass(id) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data kelas akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/manage-classes/${id}`, // Sesuaikan dengan route kelas
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Terhapus!',
                                    response.message,
                                    'success'
                                );
                                $('#classesTable').DataTable().ajax
                                    .reload(); // Pastikan ID datatable sesuai
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'Terjadi kesalahan saat menghapus data kelas.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            }


            // Handle success message
            @if (session('success'))
                toastr.success('{{ session('success') }}');
            @endif
        </script>
    @endpush
