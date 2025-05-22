{{-- resources/views/manage-student-class-assignments/create.blade.php --}}
@extends('layouts.app')

@section('title','Plotting Siswa Ke Kelas')

@section('content')
<div class="container-fluid px-4">
  <h1 class="mt-4" style="font-weight:bold;color:#183C70">
    Plotting Siswa Ke Kelas
  </h1>

  <div class="card shadow-sm mt-4">
    <div class="card-body">

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 style="font-weight:600;color:#183C70">Kelola Data Siswa</h5>
        @can('create_student')
          <a href="{{ route('manage-students.index') }}"
             class="btn btn-info btn-sm">
            Data Seluruh Siswa
          </a>
        @endcan
      </div>

      <div class="table-responsive">
        <table id="studentsTable" class="table table-bordered table-sm">
          <thead style="background:#009cf3;color:#fff">
            <tr>
              <th>No.</th>
              <th>NISN</th>
              <th>NIS</th>
              <th>Nama</th>
              <th>Jenis Kelamin</th>
              <th>Tahun Masuk</th>
              <th>Kelas</th>
              <th>Pilih</th>
            </tr>
          </thead>
          <tbody>
            <!-- Data akan diisi oleh DataTables -->
          </tbody>
        </table>
      </div>

      {{-- Kartu Konfirmasi --}}
      <div class="card bg-light mt-4 p-3" style="max-width:400px;">
        <div class="d-flex justify-content-between">
          <strong>Siswa yang dipilih :</strong>
          <span id="selectedCount">0 Siswa</span>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">
          <strong>Tindakan :</strong>
          <select id="actionSelect" class="form-select form-select-sm" style="width:auto">
            <option value="move">Pindah Kelas</option>
          </select>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">
          <strong>Tahun Akademik :</strong>
          <select id="targetYear" class="form-select form-select-sm" style="width:auto">
            <option value="">-- Pilih Tahun --</option>
            @foreach($academicYears as $ay)
              <option value="{{ $ay->id }}">{{ $ay->year }}</option>
            @endforeach
          </select>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2">
          <strong>Kelas Tujuan :</strong>
          <select id="targetClass" class="form-select form-select-sm" style="width:auto">
            <option value="">-- Pilih Kelas --</option>
            @foreach($classes as $cls)
              <option value="{{ $cls->id }}">
                {{ $cls->name }} {{ $cls->parallel_name }}
              </option>
            @endforeach
          </select>
        </div>

        <button id="confirmBtn" class="btn btn-primary w-100 mt-3">
          Konfirmasi
        </button>
      </div>

    </div>
  </div>
</div>
@endsection

@push('js')
<script>
$(function(){
  $.ajaxSetup({ headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'} });

  const table = $('#studentsTable').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    pagingType: 'simple_numbers',
    ajax: '{{ route("manage-student-class-assignments.create") }}',
    columns: [
      { data:'DT_RowIndex', name:'DT_RowIndex', className:'text-center', orderable:false, searchable:false },
      { data:'nisn', name:'nisn' },
      { data:'nis', name:'nis' },
      { data:'name', name:'name' },
      { data:'gender', name:'gender' },
      { data:'enter_year', name:'enter_year' },
      { data:'class_name', name:'class_name' },
      { data:'nisn', orderable:false, searchable:false, className:'text-center',
        render: nisn => `<input type="checkbox" class="row-select" value="${nisn}">`
      },
    ],
    order:[[0,'asc']],
    language: {
      processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
      emptyTable: 'Tidak ada data siswa',
      zeroRecords: 'Tidak ditemukan data yang sesuai',
      info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ siswa',
      infoEmpty: 'Menampilkan 0 sampai 0 dari 0 siswa',
      search: 'Pencarian:',
      paginate: { first:'Pertama', last:'Terakhir', next:'»', previous:'«' }
    },
    drawCallback: function() {
      const selected = $('.row-select:checked').map((i,e)=>e.value).get();
      $('.row-select').each(function(){
        $(this).prop('checked', selected.includes($(this).val()) );
      });
      updateCount();
    }
  });

  function updateCount(){
    $('#selectedCount').text($('.row-select:checked').length + ' Siswa');
  }

  $('#studentsTable tbody').on('change', '.row-select', updateCount);

  // Select All di luar tabel
  $('<div class="mb-3"><label><input type="checkbox" id="selectAll"> Pilih Semua</label></div>')
    .insertBefore('#studentsTable');
  $('#selectAll').change(function(){
    $('.row-select').prop('checked', this.checked);
    updateCount();
  });

  $('#confirmBtn').click(function(){
    const nisns = $('.row-select:checked').map((_,e)=>e.value).get();
    const kelas = $('#targetClass').val();
    const tahun = $('#targetYear').val();
    if (!nisns.length) return alert('Pilih minimal satu siswa.');
    if (!tahun) return alert('Pilih tahun akademik.');
    if (!kelas) return alert('Pilih kelas tujuan.');
    $.post('{{ route("manage-student-class-assignments.store") }}',
      { nisns, academic_year_id: tahun, class_id: kelas }
    )
    .done(res=>{
      alert(res.message);
      table.ajax.reload();
      $('#selectAll').prop('checked', false);
      updateCount();
    })
    .fail(xhr=>{
      alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message||xhr.responseText));
    });
  });

  // Dependent dropdown tahun → kelas
  $('#targetYear').change(function(){
    const yearId = this.value;
    if (yearId) {
      $.get('{{ route("manage-student-class-assignments.getClassesByYear") }}',{ year_id: yearId })
        .done(classes=>{
          const sel = $('#targetClass').empty().append('<option value="">-- Pilih Kelas --</option>');
          classes.forEach(c=> sel.append(`<option value="${c.id}">${c.name} ${c.parallel_name}</option>`));
        })
        .fail(xhr=>console.error('Gagal mengambil data kelas:',xhr));
    } else {
      $('#targetClass').empty().append('<option value="">-- Pilih Kelas --</option>');
    }
  });

});
</script>
@endpush
