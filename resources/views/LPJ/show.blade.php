@extends('Template.Layout')
@section('content')

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{ $title }}</h3>
                <p class="text-subtitle text-muted">{{ $lpj->nomor_lpj }}</p>
            </div>
        </div>
    </div>

    <section class="section">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row">
            {{-- KOLOM KIRI: Info + Aksi --}}
            <div class="col-lg-4">

                {{-- Status Card --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body text-center py-4">
                        <span class="badge bg-{{ $lpj->badge_status }} px-3 py-2 mb-2" style="font-size:.85rem">
                            {{ $lpj->label_status }}
                        </span>
                        <h5 class="fw-bold mb-1">{{ $lpj->nomor_lpj }}</h5>
                        <p class="text-muted small mb-0">{{ $lpj->jenis }} &mdash; {{ $lpj->nama_bulan }} {{ $lpj->periode_tahun }}</p>
                    </div>
                </div>

                {{-- Info LPJ --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-2">
                        <h6 class="mb-0 fw-semibold small">Informasi LPJ</h6>
                    </div>
                    <div class="card-body py-2">
                        <table class="table table-sm table-borderless mb-0" style="font-size:.82rem">
                            <tr>
                                <td class="text-muted ps-0">Sub Kegiatan</td>
                                <td class="fw-semibold">{{ optional(optional($lpj->anggaran)->subKegiatan)->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Saldo Awal</td>
                                <td class="fw-semibold">Rp {{ number_format($lpj->saldo_awal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Total SPJ</td>
                                <td class="fw-bold text-primary">Rp {{ number_format($lpj->total_spj, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Sisa Saldo</td>
                                <td class="fw-bold text-{{ $lpj->saldo_akhir >= 0 ? 'success' : 'danger' }}">
                                    Rp {{ number_format($lpj->saldo_akhir, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Dibuat Oleh</td>
                                <td>{{ optional($lpj->pembuat)->fullname ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Tanggal Buat</td>
                                <td>{{ $lpj->created_at->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Riwayat Persetujuan --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-2">
                        <h6 class="mb-0 fw-semibold small">Riwayat Persetujuan</h6>
                    </div>
                    <div class="card-body py-2" style="font-size:.8rem">
                        <div class="d-flex gap-2 mb-2">
                            <div class="timeline-dot mt-1 {{ $lpj->tgl_verifikasi ? 'bg-primary' : 'bg-light border' }}" style="width:10px;height:10px;border-radius:50%;flex-shrink:0"></div>
                            <div>
                                <div class="fw-semibold">Verifikasi PPK</div>
                                @if($lpj->ppk)
                                    <div class="text-muted">{{ optional($lpj->ppk)->fullname }}</div>
                                    <div class="text-muted">{{ $lpj->tgl_verifikasi ? $lpj->tgl_verifikasi->format('d M Y H:i') : '-' }}</div>
                                    @if($lpj->catatan_ppk)
                                    <div class="alert alert-light py-1 px-2 mt-1 mb-0 small">{{ $lpj->catatan_ppk }}</div>
                                    @endif
                                @else
                                    <div class="text-muted fst-italic">Menunggu...</div>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="timeline-dot mt-1 {{ $lpj->tgl_approval ? 'bg-success' : 'bg-light border' }}" style="width:10px;height:10px;border-radius:50%;flex-shrink:0"></div>
                            <div>
                                <div class="fw-semibold">Approval PA/KPA</div>
                                @if($lpj->pa)
                                    <div class="text-muted">{{ optional($lpj->pa)->fullname }}</div>
                                    <div class="text-muted">{{ $lpj->tgl_approval ? $lpj->tgl_approval->format('d M Y H:i') : '-' }}</div>
                                    @if($lpj->catatan_pa)
                                    <div class="alert alert-light py-1 px-2 mt-1 mb-0 small">{{ $lpj->catatan_pa }}</div>
                                    @endif
                                @else
                                    <div class="text-muted fst-italic">Menunggu...</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TOMBOL AKSI sesuai STATUS & ROLE --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body">

                        {{-- Tombol Status LPJ --}}
                        @if($lpj->status == 'revisi')

                            <div class="mb-2">

                                <span class="badge bg-danger">
                                    Perlu Revisi PA/KPA
                                </span>

                                <a href="{{ route('lpj.create') }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i> Perbaiki LPJ
                                </a>

                                <form action="{{ route('lpj.ajukan', $lpj->id) }}"
                                    method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-primary">
                                        <i class="bi bi-send"></i> Ajukan Ulang
                                    </button>
                                </form>

                            </div>

                        @endif

                        {{-- BENDAHARA: Ajukan --}}
                        @if(in_array($lpj->status, ['draft','revisi']) && in_array($role, ['User','Ppk','Admin']))
                        <form method="POST" action="{{ route('lpj.ajukan', $lpj->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 mb-2"
                                    onclick="return confirm('Ajukan LPJ ini ke PPK?')">
                                <i class="bi bi-send me-1"></i> Ajukan ke PPK
                            </button>
                        </form>
                        @endif

                        {{-- PPK: Verifikasi --}}
                        @if($lpj->status == 'diajukan' && in_array($role, ['ppk','admin']))
                        <a href="{{ route('lpj.verifikasi_ppk.form', $lpj->id) }}" class="btn btn-warning w-100 mb-2">
                            <i class="bi bi-check2-square me-1"></i> Verifikasi (PPK)
                        </a>
                        @endif

                        {{-- PPK: Lanjut verifikasi --}}
                        @if($lpj->status == 'verifikasi_ppk' && in_array($role, ['ppk','admin']))
                        <a href="{{ route('lpj.verifikasi_ppk.form', $lpj->id) }}" class="btn btn-warning w-100 mb-2">
                            <i class="bi bi-check2-square me-1"></i> Lanjut Verifikasi
                        </a>
                        @endif

                        {{-- PA/KPA: Approval --}}
                        @if($lpj->status == 'disetujui_ppk' && in_array($role, ['pa','kpa','admin']))
                        <a href="{{ route('lpj.approval_pa.form', $lpj->id) }}" class="btn btn-success w-100 mb-2">
                            <i class="bi bi-patch-check me-1"></i> Approval PA/KPA
                        </a>
                        @endif

                        {{-- Generate PDF --}}
                        @if($lpj->status == 'approved' && in_array($role, ['user','bendahara','admin']))
                        <form method="POST" action="{{ route('lpj.generate', $lpj->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-info w-100 mb-2 text-white">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Generate PDF
                            </button>
                        </form>
                        @endif

                        {{-- Cetak --}}
                        @if(in_array($lpj->status, ['generated','selesai']))
                        <a href="{{ route('lpj.cetak', $lpj->id) }}" class="btn btn-success w-100 mb-2">
                            <i class="bi bi-printer me-1"></i> Cetak LPJ
                            @if($lpj->jumlah_cetak > 0)
                            <span class="badge bg-white text-success ms-1">{{ $lpj->jumlah_cetak }}x</span>
                            @endif
                        </a>
                        <a href="{{ route('lpj.preview', $lpj->id) }}" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                            <i class="bi bi-eye me-1"></i> Preview PDF
                        </a>
                        @endif

                        <a href="{{ route('lpj.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN: Daftar SPJ --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-list-ul me-2 text-primary"></i>
                            Daftar SPJ dalam LPJ Ini
                            <span class="badge bg-primary ms-1">{{ $lpj->spjList->count() }}</span>
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="font-size:.74rem">No</th>
                                    <th style="font-size:.74rem">No. Kwitansi</th>
                                    <th style="font-size:.74rem">Uraian</th>
                                    <th style="font-size:.74rem">Nama Penerima</th>
                                    <th style="font-size:.74rem">Jenis</th>
                                    <th style="font-size:.74rem">Total</th>
                                    <th style="font-size:.74rem">Tanggal</th>
                                    @if(in_array($lpj->status, ['draft','revisi']))
                                    <th style="font-size:.74rem">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($lpj->spjList as $i => $spj)
                                <tr>
                                    <td class="ps-3 text-muted" style="font-size:.8rem">{{ $i + 1 }}</td>
                                    <td class="fw-semibold" style="font-size:.8rem">{{ $spj->nomor_kwitansi ?? $spj->nomor_spj ?? '-' }}</td>
                                    <td style="font-size:.8rem">{{ \Illuminate\Support\Str::limit($spj->uraian ?? '-', 35) }}</td>
                                    <td style="font-size:.8rem">{{ \Illuminate\Support\Str::limit($spj->nama_penerima ?? '-', 25) }}</td>
                                    <td>
                                        @php
                                            $sd = strtoupper($spj->sumber_dana ?? '');
                                            if ($sd == 'GU') { $sCls = 'primary'; }
                                            elseif ($sd == 'LS') { $sCls = 'success'; }
                                            elseif ($sd == 'TU') { $sCls = 'warning'; }
                                            else { $sCls = 'secondary'; }
                                        @endphp
                                        <span class="badge bg-light-{{ $sCls }} text-{{ $sCls }}">{{ $sd ?: '-' }}</span>
                                    </td>
                                    <td class="fw-bold" style="font-size:.8rem;color:#435ebe">
                                        Rp {{ number_format($spj->total ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="text-muted" style="font-size:.75rem">
                                        {{ \Carbon\Carbon::parse($spj->tanggal)->format('d M Y') }}
                                    </td>
                                    @if(in_array($lpj->status, ['draft','revisi']))
                                    {{-- <td>
                                        <form method="POST" action="{{ route('lpj.hapus_spj', [$lpj->id, $spj->id]) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light-danger text-danger"
                                                    onclick="return confirm('Lepas SPJ ini dari LPJ?')" title="Lepas SPJ">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    </td> --}}
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox d-block fs-3 mb-1"></i>Belum ada SPJ.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold pe-3 ps-3" style="font-size:.82rem">TOTAL</td>
                                    <td class="fw-bold" style="color:#435ebe;font-size:.85rem">
                                        Rp {{ number_format($lpj->total_spj, 0, ',', '.') }}
                                    </td>
                                    <td colspan="{{ in_array($lpj->status, ['draft','revisi']) ? 2 : 1 }}"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection