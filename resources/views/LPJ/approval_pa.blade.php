@extends('Template.Layout')
@section('content')

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{ $title }}</h3>
                <p class="text-subtitle text-muted">Validasi dan persetujuan akhir LPJ oleh PA/KPA</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/home') }}">{{ $breadcumd }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('lpj.index') }}">{{ $breadcumd1 }}</a></li>
                        <li class="breadcrumb-item active">{{ $breadcumd2 }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">

            <div class="col-lg-4">
                {{-- Info LPJ --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header py-2" style="background:#28c76f;color:#fff">
                        <h6 class="mb-0 small"><i class="bi bi-patch-check me-2"></i>LPJ untuk Disetujui PA/KPA</h6>
                    </div>
                    <div class="card-body py-2">
                        <table class="table table-sm table-borderless mb-0" style="font-size:.82rem">
                            <tr><td class="text-muted ps-0">Nomor LPJ</td><td class="fw-bold">{{ $lpj->nomor_lpj }}</td></tr>
                            <tr><td class="text-muted ps-0">Jenis</td><td>{{ $lpj->jenis }}</td></tr>
                            <tr><td class="text-muted ps-0">Periode</td><td>{{ $lpj->nama_bulan }} {{ $lpj->periode_tahun }}</td></tr>
                            <tr><td class="text-muted ps-0">Bendahara</td><td>{{ optional($lpj->pembuat)->fullname ?? '-' }}</td></tr>
                            <tr><td class="text-muted ps-0">Diverif. PPK</td><td>{{ optional($lpj->ppk)->fullname ?? '-' }}</td></tr>
                            <tr><td class="text-muted ps-0">Tgl Verifikasi</td><td>{{ $lpj->tgl_verifikasi ? $lpj->tgl_verifikasi->format('d M Y') : '-' }}</td></tr>
                            <tr><td class="text-muted ps-0">Jumlah SPJ</td><td><span class="badge bg-success">{{ $lpj->spjList->count() }} SPJ</span></td></tr>
                            <tr><td class="text-muted ps-0">Total</td><td class="fw-bold" style="color:#28c76f">Rp {{ number_format($lpj->total_spj, 0, ',', '.') }}</td></tr>
                            <tr><td class="text-muted ps-0">Sisa Saldo</td><td class="fw-bold">Rp {{ number_format($lpj->saldo_akhir, 0, ',', '.') }}</td></tr>
                        </table>
                    </div>
                </div>

                @if($lpj->catatan_ppk)
                <div class="alert alert-info py-2 small mb-3">
                    <div class="fw-semibold mb-1"><i class="bi bi-chat-left-text me-1"></i>Catatan PPK:</div>
                    {{ $lpj->catatan_ppk }}
                </div>
                @endif

                {{-- Form Approval --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-2">
                        <h6 class="mb-0 fw-semibold small">Keputusan PA/KPA</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('lpj.approval_pa.proses', $lpj->id) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Catatan PA/KPA</label>
                                <textarea name="catatan_pa" class="form-control" rows="4"
                                          placeholder="Catatan persetujuan atau penolakan...">{{ old('catatan_pa') }}</textarea>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" name="keputusan" value="setuju"
                                        class="btn btn-success"
                                        onclick="return confirm('Setujui LPJ ini? LPJ akan siap untuk di-generate.')">
                                    <i class="bi bi-check-circle-fill me-1"></i> Setujui LPJ
                                </button>
                                <button type="submit" name="keputusan" value="tolak"
                                        class="btn btn-outline-danger"
                                        onclick="return confirm('Tolak LPJ ini?')">
                                    <i class="bi bi-x-circle me-1"></i> Tolak LPJ
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
                            <i class="bi bi-list-ul me-2 text-success"></i>
                            Rincian SPJ dalam LPJ
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
                                        <span class="badge bg-light-success text-success">{{ $sd }}</span>
                                    </td>
                                    <td class="fw-bold" style="font-size:.8rem;color:#28c76f">Rp {{ number_format($spj->total ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-muted" style="font-size:.75rem">{{ \Carbon\Carbon::parse($spj->tanggal)->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada SPJ.</td></tr>
                            @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold pe-3 ps-3" style="font-size:.82rem">TOTAL</td>
                                    <td class="fw-bold" style="color:#28c76f">Rp {{ number_format($lpj->total_spj, 0, ',', '.') }}</td>
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