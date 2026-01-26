@extends('Template.Layout')
@section('content')

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-7">
                <h4 class="card-title">{{ $title }}</h4>
            </div>
            <div class="col-md-5 text-end">
                <button class="btn btn-outline-primary btn-sm" id="createPptk">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </div>
        </div>

        <table id="tabelPptk" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Jabatan</th>
                    <th>Unit</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@include('Master_Data.Pptk.modal')
@include('Master_Data.Pptk.Fungsi')

@endsection
