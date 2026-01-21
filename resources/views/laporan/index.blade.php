@extends('Template.Layout')
@section('content')

<div class="card">
    <div class="card-body">
        {{-- <div class="row">
            <div class="col-md-2">
                <h4 class="card-title">{{ $title }}</h4>
            </div>
            <div class="col-md-9"></div>
            <div class="col-md-1">
                <a href="javascript:void(0)" id="createRka" class="btn btn-outline-primary btn-tone m-r-5 btn-xs ml-auto">
                    <i class="fas fa-pencil-alt"></i>
                </a>
            </div>
        </div> --}}

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div class="d-flex align-items-center gap-2">
                <label class="fw-bold">Tahap:</label>
                <select id="selectTahap" class="form-select form-select-sm" style="width:150px;">
                    @foreach($rka_locks as $lk)
                        <option value="{{ $lk->tahap }}" 
                            {{ $lk->is_active ? 'selected' : '' }}>
                            RKA {{ ucfirst($lk->tahap) }}
                        </option>
                    @endforeach
                </select>

                <button class="btn btn-sm btn-primary" id="btnSetAktif">
                    <i class="bi bi-check2-circle"></i> Set Tahap Aktif
                </button>

                <button class="btn btn-sm btn-warning" id="btnToggleLock">
                    <i class="bi bi-lock-fill"></i> Kunci / Buka Kunci Tahap
                </button>
            </div>

            @if(!$active_lock || $active_lock->is_locked == 0)
                <a href="javascript:void(0)" id="createRka" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-plus-square"></i> Tambah RKA
                </a>
            @else
                <button class="btn btn-outline-secondary btn-sm" disabled>
                    <i class="bi bi-lock-fill"></i> Tahap Terkunci
                </button>
            @endif
        </div>

        <br><br>
        <div class="m-t-25 table-responsive">
            <table id="tabelrka" class="table table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Program</th>
                        <th>Kegiatan</th>
                        <th>Sub Kegiatan</th>
                        <th>Sumber Dana</th>
                        <th width="100px">Pagu</th>
                        <th width="100px">Realisasi</th>
                        <th width="100px">Sisa Pagu</th>
                        <th width="100px">PPTK</th>
                        <th class="text-center" width="150px">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@include('laporan.Modal.Tambah')
@include('laporan.Fungsi.Fungsi')

@endsection
