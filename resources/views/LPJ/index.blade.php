@extends('Template.Layout')
@section('content')

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{ $title }}</h3>
                <p class="text-subtitle text-muted">Daftar Laporan Pertanggungjawaban TA {{ $tahun }}</p>
            </div>
        </div>
    </div>

    <section class="section">

        {{-- STATISTIK --}}
        <div class="row mb-3">
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="avatar bg-light-secondary rounded p-2">
                            <i class="bi bi-file-earmark-text fs-4 text-secondary"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em">Draft</p>
                            <h4 class="mb-0 fw-bold">{{ $stat['draft'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="avatar bg-light-warning rounded p-2">
                            <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em">Dalam Proses</p>
                            <h4 class="mb-0 fw-bold text-warning">{{ $stat['proses'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="avatar bg-light-success rounded p-2">
                            <i class="bi bi-check-circle fs-4 text-success"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em">Disetujui</p>
                            <h4 class="mb-0 fw-bold text-success">{{ $stat['approved'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="avatar bg-light-danger rounded p-2">
                            <i class="bi bi-arrow-counterclockwise fs-4 text-danger"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em">Perlu Revisi</p>
                            <h4 class="mb-0 fw-bold text-danger">{{ $stat['revisi'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER + TOMBOL BUAT --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
                <h5 class="mb-0 fw-semibold">Daftar LPJ</h5>
                @if(in_array($role, ['user','bendahara','admin']))
                <a href="{{ route('lpj.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Buat LPJ Baru
                </a>
                @endif
            </div>

            {{-- Filter --}}
            <div class="card-body border-bottom pb-3">
                <form method="GET" action="{{ route('lpj.index') }}" class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label small mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm" style="min-width:150px">
                            <option value="">Semua Status</option>
                            <option value="draft"          {{ request('status')=='draft'          ? 'selected':'' }}>Draft</option>
                            <option value="diajukan"       {{ request('status')=='diajukan'       ? 'selected':'' }}>Diajukan</option>
                            <option value="verifikasi_ppk" {{ request('status')=='verifikasi_ppk' ? 'selected':'' }}>Verifikasi PPK</option>
                            <option value="revisi"         {{ request('status')=='revisi'         ? 'selected':'' }}>Revisi</option>
                            <option value="disetujui_ppk"  {{ request('status')=='disetujui_ppk'  ? 'selected':'' }}>Disetujui PPK</option>
                            <option value="validasi_pa"    {{ request('status')=='validasi_pa'    ? 'selected':'' }}>Validasi PA</option>
                            <option value="approved"       {{ request('status')=='approved'       ? 'selected':'' }}>Approved</option>
                            <option value="generated"      {{ request('status')=='generated'      ? 'selected':'' }}>Siap Cetak</option>
                            <option value="selesai"        {{ request('status')=='selesai'        ? 'selected':'' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-1">Jenis</label>
                        <select name="jenis" class="form-select form-select-sm">
                            <option value="">Semua Jenis</option>
                            <option value="GU" {{ request('jenis')=='GU' ? 'selected':'' }}>GU</option>
                            <option value="LS" {{ request('jenis')=='LS' ? 'selected':'' }}>LS</option>
                            <option value="TU" {{ request('jenis')=='TU' ? 'selected':'' }}>TU</option>
                            <option value="UP" {{ request('jenis')=='UP' ? 'selected':'' }}>UP</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small mb-1">Bulan</label>
                        <select name="bulan" class="form-select form-select-sm">
                            <option value="">Semua Bulan</option>
                            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                            <option value="{{ $i+1 }}" {{ request('bulan')==($i+1) ? 'selected':'' }}>{{ $bln }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
                        <a href="{{ route('lpj.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
                    </div>
                </form>
            </div>

            {{-- TABLE --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="font-size:.75rem">No</th>
                                <th style="font-size:.75rem">Nomor LPJ</th>
                                <th style="font-size:.75rem">Jenis</th>
                                <th style="font-size:.75rem">Periode</th>
                                <th style="font-size:.75rem">Sub Kegiatan</th>
                                <th style="font-size:.75rem">Jumlah SPJ</th>
                                <th style="font-size:.75rem">Total</th>
                                <th style="font-size:.75rem">Status</th>
                                <th style="font-size:.75rem">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($lpjList as $i => $lpj)
                            <tr>
                                <td class="ps-3 text-muted" style="font-size:.8rem">{{ $lpjList->firstItem() + $i }}</td>
                                <td>
                                    <span class="fw-semibold" style="font-size:.82rem">{{ $lpj->nomor_lpj }}</span>
                                </td>
                                <td>
                                    @php
                                        $jenis = $lpj->jenis;
                                        if ($jenis == 'GU') { $jCls = 'primary'; }
                                        elseif ($jenis == 'LS') { $jCls = 'success'; }
                                        elseif ($jenis == 'TU') { $jCls = 'warning'; }
                                        else { $jCls = 'secondary'; }
                                    @endphp
                                    <span class="badge bg-light-{{ $jCls }} text-{{ $jCls }}">{{ $jenis }}</span>
                                </td>
                                <td style="font-size:.8rem">{{ $lpj->nama_bulan }} {{ $lpj->periode_tahun }}</td>
                                <td style="font-size:.8rem;max-width:180px">
                                    {{ \Illuminate\Support\Str::limit(optional($lpj->anggaran->subKegiatan ?? null)->nama ?? '-', 35) }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light-primary text-primary">{{ $lpj->spjList->count() }} SPJ</span>
                                </td>
                                <td style="font-size:.8rem;font-weight:600">
                                    Rp {{ number_format($lpj->total_spj, 0, ',', '.') }}
                                </td>
                                <td>
                                    <span class="badge bg-light-{{ $lpj->badge_status }} text-{{ $lpj->badge_status }}" style="font-size:.72rem">
                                        {{ $lpj->label_status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        {{-- DETAIL --}}
                                        <a href="{{ route('lpj.show', $lpj->id) }}"
                                        class="btn btn-sm btn-light-primary" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        {{-- PREVIEW --}}
                                        @if(in_array($lpj->status, ['approved','generated','selesai']))
                                            <a href="{{ route('lpj.preview', $lpj->id) }}"
                                            target="_blank"
                                            class="btn btn-sm btn-light-success" title="Preview PDF">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                        @endif

                                        {{-- HAPUS LPJ 🔥 --}}
                                        @if(in_array($lpj->status, ['draft','diajukan','revisi']))
                                            <button 
                                                class="btn btn-sm btn-light-danger btn-hapus-lpj"
                                                data-id="{{ $lpj->id }}"
                                                title="Hapus LPJ">

                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Belum ada data LPJ.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($lpjList->hasPages())
                <div class="card-footer bg-white border-top">
                    {{ $lpjList->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </section>
</div>

@include('LPJ.ajax')

@endsection
