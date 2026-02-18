@extends('Template.Layout')
@section('content')

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{ $title }}</h3>
                <p class="text-subtitle text-muted">Verifikasi kelengkapan dan kebenaran dokumen SPJ</p>
            </div>
            {{-- <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/home') }}">{{ $breadcumd }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('lpj.index') }}">{{ $breadcumd1 }}</a></li>
                        <li class="breadcrumb-item active">{{ $breadcumd2 }}</li>
                    </ol>
                </nav>
            </div> --}}
        </div>
    </div>

    <section class="section">
        <div class="row">

            {{-- Kiri: Info + Form Keputusan --}}
            <div class="col-lg-4">
                {{-- Info LPJ --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header py-2" style="background:#435ebe;color:#fff">
                        <h6 class="mb-0 small"><i class="bi bi-file-earmark-check me-2"></i>LPJ yang Diverifikasi</h6>
                    </div>
                    <div class="card-body py-2">
                        <table class="table table-sm table-borderless mb-0" style="font-size:.82rem">
                            <tr><td class="text-muted ps-0">Nomor LPJ</td><td class="fw-bold">{{ $lpj->nomor_lpj }}</td></tr>
                            <tr><td class="text-muted ps-0">Jenis</td><td>{{ $lpj->jenis }}</td></tr>
                            <tr><td class="text-muted ps-0">Periode</td><td>{{ $lpj->nama_bulan }} {{ $lpj->periode_tahun }}</td></tr>
                            <tr><td class="text-muted ps-0">Bendahara</td><td>{{ optional($lpj->pembuat)->fullname ?? '-' }}</td></tr>
                            <tr><td class="text-muted ps-0">Jumlah SPJ</td><td><span class="badge bg-primary">{{ $lpj->spjList->count() }} SPJ</span></td></tr>
                            <tr><td class="text-muted ps-0">Total</td><td class="fw-bold text-primary">Rp {{ number_format($lpj->total_spj, 0, ',', '.') }}</td></tr>
                        </table>
                    </div>
                </div>

                {{-- Checklist Verifikasi --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-2">
                        <h6 class="mb-0 fw-semibold small">Checklist Verifikasi PPK</h6>
                    </div>
                    <div class="card-body py-2">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="chk1">
                            <label class="form-check-label small" for="chk1">Kelengkapan dokumen kwitansi</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="chk2">
                            <label class="form-check-label small" for="chk2">Kesesuaian nilai dengan anggaran</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="chk3">
                            <label class="form-check-label small" for="chk3">Kebenaran perhitungan pajak</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="chk4">
                            <label class="form-check-label small" for="chk4">BAPP/BAPB/BAST (jika ada)</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="chk5">
                            <label class="form-check-label small" for="chk5">Tanda tangan penerima lengkap</label>
                        </div>
                    </div>
                </div>

                {{-- Form Keputusan --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-2">
                        <h6 class="mb-0 fw-semibold small">Keputusan PPK</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('lpj.verifikasi_ppk.proses', $lpj->id) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Catatan PPK</label>
                                <textarea name="catatan_ppk" class="form-control" rows="4"
                                          placeholder="Catatan verifikasi (opsional untuk setuju, wajib jika revisi)">{{ old('catatan_ppk') }}</textarea>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" name="keputusan" value="setuju"
                                        class="btn btn-success"
                                        onclick="return confirm('Setujui LPJ ini dan teruskan ke PA/KPA?')">
                                    <i class="bi bi-check-circle me-1"></i> Setujui & Teruskan ke PA/KPA
                                </button>
                                <button type="submit" name="keputusan" value="revisi"
                                        class="btn btn-outline-danger"
                                        onclick="return confirm('Kembalikan ke Bendahara untuk direvisi?')">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Kembalikan untuk Revisi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Kanan: Tabel SPJ --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-list-check me-2 text-primary"></i>
                            Daftar SPJ untuk Diverifikasi
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
                                    <th style="font-size:.74rem">Tgl</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($lpj->spjList as $i => $spj)
                                <tr>
                                    <td class="ps-3 text-muted" style="font-size:.8rem">{{ $i+1 }}</td>
                                    <td class="fw-semibold" style="font-size:.8rem">{{ $spj->nomor_kwitansi ?? '-' }}</td>
                                    <td style="font-size:.8rem">{{ \Illuminate\Support\Str::limit($spj->uraian ?? '-', 35) }}</td>
                                    <td style="font-size:.8rem">{{ \Illuminate\Support\Str::limit($spj->nama_penerima ?? '-', 22) }}</td>
                                    <td>
                                        @php $sd = strtoupper($spj->sumber_dana ?? ''); @endphp
                                        <span class="badge bg-light-primary text-primary">{{ $sd }}</span>
                                    </td>
                                    <td class="fw-bold" style="font-size:.8rem;color:#435ebe">Rp {{ number_format($spj->total ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-muted" style="font-size:.75rem">{{ \Carbon\Carbon::parse($spj->tanggal)->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada SPJ.</td></tr>
                            @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold pe-3 ps-3" style="font-size:.82rem">TOTAL</td>
                                    <td class="fw-bold" style="color:#435ebe">Rp {{ number_format($lpj->total_spj, 0, ',', '.') }}</td>
                                    <td></td>
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