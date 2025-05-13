@extends('layouts.app')

@section('title', 'Manajemen Data Mata Pelajaran')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 text-uppercase">
                <h4 class="m-0">Manajemen Data Mata Pelajaran</h4>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Kelola Data Mata Pelajaran</h3>
                <a href="{{ route('manage-subjects.create') }}" class="btn btn-sm btn-primary float-right">
                    <i class="fas fa-plus"></i> Tambah Data
                </a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="subjectsTable">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Kode Mata Pelajaran</th>
                            <th class="text-center">Nama Mata Pelajaran</th>
                            <th class="text-center">Deskripsi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Tambahan style untuk warna header tabel dan perataan "Show entries" + Search + Pagination -->
<style>
    /* Warna background header tabel */
    #subjectsTable thead th {
        background-color: #009cf3;
        color: white;
    }

    /* Label "Show entries" */
    div.dataTables_length label {
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    /* Dropdown "Show entries" */
    div.dataTables_length select {
        width: auto;
        min-width: 70px;
    }

    /* Perataan Show entries dan Search ke kiri-kanan */
    div.dataTables_wrapper .row:first-child {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    /* Atur input search */
    div.dataTables_filter {
        text-align: right;
    }

    div.dataTables_filter label {
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    /* Geser pagination ke pojok kanan */
    div.dataTables_paginate {
        text-align: right;
        float: right;
    }
</style>

@endsection

@push('js')
<script>
    $(function () {
        $('#subjectsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('subjects.index') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'kode', name: 'kode' },
                { data: 'nama', name: 'nama' },
                { data: 'deskripsi', name: 'deskripsi' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });
    });

    function deleteSubject(id) {
        if (confirm('Yakin ingin menghapus mata pelajaran ini?')) {
            $.ajax({
                url: `/manage-subjects/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    alert(response.message);
                    window.location.href = '/manage-subjects'; // ⬅️ Redirect setelah berhasil
                },
                error: function() {
                    toastr.error('Gagal menghapus data.');
                }
            });
        }
    }
</script>
@endpush