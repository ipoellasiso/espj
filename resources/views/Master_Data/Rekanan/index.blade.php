@extends('Template.Layout')
@section('content')

<style>
    select.form-control {
        border: 1px solid #ced4da !important;
        border-radius: 6px;
        height: 38px;
    }
</style>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-7">
                <h4 class="card-title">{{ $title }}</h4>
            </div>
            <div class="col-md-5 text-end">
                <button class="btn btn-outline-primary btn-sm" id="createRekanan">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </div>
        </div>

        <table id="tabelRekanan" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Rekanan</th>
                    <th>Alamat</th>
                    <th>NPWP</th>
                    <th>Unit</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@include('Master_Data.Rekanan.modal')
@include('Master_Data.Rekanan.Fungsi')

@endsection
