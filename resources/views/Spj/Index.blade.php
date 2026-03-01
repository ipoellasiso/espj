@extends('Template.Layout')
@section('content')

<style>
/* Bungkus teks uraian agar turun ke bawah */
td.wrap-uraian {
    white-space: normal !important;
    word-break: break-word !important;
    white-space: pre-line !important;
    max-width: 350px;   /* Bisa disesuaikan */
}

.select2-container .select2-selection--single {
    height: 38px !important;
    padding: 4px 10px !important;
    border: 1px solid #ced4da !important;
}

.select2-container--bootstrap-5 .select2-selection {
    min-height: 38px !important;
}

.select2-container--bootstrap-5.select2-container--focus 
.select2-selection {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 0.2rem rgba(13,110,253,.25) !important;
}

.select2-container--bootstrap-5 .select2-selection:hover {
    border-color: #86b7fe;
}

.select2-container--bootstrap-5 
.select2-selection--multiple {
    min-height: 38px !important;
    padding-top: 2px;
}

.select2-container--bootstrap-5 
.select2-selection--multiple 
.select2-search__field {
    height: 26px !important;
    width: 100% !important;
    margin-top: 4px;
    font-size: 14px;
}

.select2-container--bootstrap-5 
.select2-selection--multiple {
    min-height: 38px !important;
    padding: 4px 8px !important;
}

.select2-search--inline {
    line-height: 24px !important;
}

.select2-search__field {
    padding-left: 4px !important;
}

.select2-container--bootstrap-5 
.select2-selection--multiple 
.select2-search--inline {
    flex: 1 0 100% !important;
    width: 100% !important;
}

.select2-selection--multiple {
    display: flex !important;
    flex-wrap: wrap !important;
}

.select2-container--bootstrap-5 
.select2-selection--multiple {
    border: 1px solid #ced4da !important;
    border-radius: 0.375rem !important;
}

.select2-container--bootstrap-5.select2-container--focus 
.select2-selection--multiple {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 0.2rem rgba(13,110,253,.25) !important;
}

.select2-container--bootstrap-5 
.select2-selection--multiple:hover {
    border-color: #86b7fe !important;
}

.select2-selection--multiple {
    padding: 4px 8px !important;
    min-height: 38px !important;
}

</style>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <h4 class="card-title">{{ $title ?? 'Data SPJ' }}</h4>
            </div>
            <div class="col-md-8"></div>
            <div class="col-md-1">
                <a href="javascript:void(0)" id="createSpj" class="btn btn-outline-primary btn-tone m-r-5 btn-xs ml-auto">
                    <i class="fas fa-pencil-alt"></i>
                </a>
            </div>
        </div>

        <br>
        <div class="m-t-25 table-responsive">
            <table id="tabelSpj" class="table table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Nomor SPJ</th>
                        <th>Tanggal</th>
                        <th>Uraian</th>
                        <th>Total</th>
                        <th>Unit</th>
                        <th class="text-center" width="150px">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@include('Spj.Modal.Tambah')
@include('Spj.Fungsi.Fungsi')

@endsection
