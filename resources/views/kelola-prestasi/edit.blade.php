@extends('layouts.app')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

    body, input, button, select, textarea {
        font-family: 'Roboto', sans-serif !important;
    }

    .content-section {
        background-color: #f4f6f9;
        padding: 60px 0;
    }

    .title-prestasi {
        font-size: 36px;
        font-weight: 700;
        color: #003366;
        margin-bottom: 32px;
        text-align: left;
        margin-left: 60px; /* selaraskan dengan tambah */
    }

    .prestasi-card-wrapper {
        margin: 0 auto;
        padding-left: 60px;
        padding-right: 60px;
    }

    .prestasi-card {
        background-color: #fff;
        border-radius: 16px;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
        padding: 48px;
        position: relative;
        width: 100%;
    }

    .prestasi-card .form-group {
        margin-bottom: 28px;
    }

    .prestasi-card .form-group label {
        font-size: 18px;
        font-weight: 500;
        color: #003366;
        display: block;
        margin-bottom: 12px;
    }

    .prestasi-card .form-control {
        font-size: 18px;
        padding: 16px 20px;
        border-radius: 10px;
        border: 1px solid #ced4da;
        width: 100%;
    }

    .prestasi-card .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn-save {
        background-color: #007bff;
        color: #fff;
        font-size: 18px;
        font-weight: 600;
        border: none;
        padding: 16px;
        border-radius: 10px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: background-color 0.2s ease;
    }

    .btn-save:hover {
        background-color: #0056b3;
    }

    .back-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        background-color: #e9f2ff;
        border: none;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #007bff;
        font-size: 20px;
        transition: background-color 0.2s ease;
    }

    .back-btn:hover {
        background-color: #d0e7ff;
    }
</style>

<div class="content-section">
    <div class="container-fluid">
        <div class="title-prestasi">Edit Data Prestasi</div>
        <div class="prestasi-card-wrapper">
            <div class="prestasi-card">
                <button type="button" onclick="window.history.back()" class="back-btn">
                    <i class="fa fa-arrow-left"></i>
                </button>
                <form method="POST" action="{{ route('prestasi.update', $prestasi->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="jenis_prestasi">Jenis Prestasi</label>
                        <input type="text" id="jenis_prestasi" name="jenis_prestasi" class="form-control" value="{{ $prestasi->jenis_prestasi }}" required>
                    </div>
                    <div class="form-group">
                        <label for="kategori_prestasi">Kategori Prestasi</label>
                        <input type="text" id="kategori_prestasi" name="kategori_prestasi" class="form-control" value="{{ $prestasi->kategori_prestasi }}" required>
                    </div>
                    <div class="form-group">
                        <label for="poin">Poin</label>
                        <input type="number" id="poin" name="poin" class="form-control" value="{{ $prestasi->poin }}" required>
                    </div>
                    <button type="submit" class="btn-save">
                        <i class="fa fa-save"></i> SIMPAN PERUBAHAN
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
