@extends('Template.Layout')
@section('content')

<div class="card">
    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-3">
                <label>Tanggal Awal</label>
                <input type="date" id="tgl_awal" class="form-control">
            </div>

            <div class="col-md-3">
                <label>Tanggal Akhir</label>
                <input type="date" id="tgl_akhir" class="form-control">
            </div>

            <div class="col-md-6 d-flex align-items-end">
                <button class="btn btn-primary me-2" id="filterPajak">
                    Filter
                </button>

                <button class="btn btn-success" id="exportExcel">
                    Export Excel
                </button>
            </div>
        </div>

        <table id="tabelRegisterPajak" class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor SPJ</th>
                    <th>Tanggal</th>
                    <th>Rekanan</th>
                    <th>Jenis Pajak</th>
                    <th>Nilai Pajak</th>
                    <th>E-Billing</th>
                    <th>NTPN</th>
                </tr>
            </thead>
        </table>

    </div>
</div>

@include('Pajak.Register.Fungsi')

@endsection