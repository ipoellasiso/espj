@extends('Template.Layout')
@section('content')

<style>
/* ═══ MAZER COMPATIBLE THEME ═══
   Mengikuti palet warna template Mazer:
   - Background: #f8f8f8 (body Mazer)
   - Card: #ffffff dengan shadow halus
   - Accent biru: #435ebe (primary Mazer)
   - Teks gelap: #404040
   - Muted: #9e9e9e
*/

:root {
    --mz-bg:        #f4f5f9;
    --mz-card:      #ffffff;
    --mz-card2:     #f8f9fc;
    --mz-card3:     #eef0f7;
    --mz-blue:      #435ebe;
    --mz-blue-lt:   #e8ecfb;
    --mz-teal:      #00b8d9;
    --mz-teal-lt:   #e0f7fb;
    --mz-orange:    #ff9f43;
    --mz-orange-lt: #fff3e0;
    --mz-red:       #ea5455;
    --mz-red-lt:    #fdecea;
    --mz-green:     #28c76f;
    --mz-green-lt:  #e6f9ef;
    --mz-text:      #404040;
    --mz-muted:     #9e9e9e;
    --mz-border:    #e7eaf0;
    --mz-shadow:    0 2px 12px rgba(0,0,0,0.07);
    --mz-shadow-h:  0 6px 20px rgba(67,94,190,0.15);
}

*,*::before,*::after { box-sizing: border-box; }

.dash-wrap {
    font-family: inherit !important;
    color: var(--mz-text);
    padding: 0 0 2rem;
}

/* ── HEADER ── */
.dash-header {
    display: flex; align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap; gap: 1rem;
    margin-bottom: 1.5rem; padding-top: .25rem;
}
.dash-header-left h1 {
    font-size: 1.35rem; font-weight: 700;
    color: var(--mz-text); margin: 0 0 3px; line-height: 1.2;
}
.dash-header-left p { color: var(--mz-muted); font-size: .8rem; margin: 0; }
.header-right { display: flex; gap: .5rem; align-items: center; flex-wrap: wrap; }
.badge-pill {
    background: var(--mz-blue-lt);
    border: 1px solid rgba(67,94,190,.2);
    border-radius: 50px; padding: 4px 13px;
    font-size: .72rem; font-weight: 600;
    color: var(--mz-blue); white-space: nowrap;
}
.badge-pill.tahun {
    background: var(--mz-orange-lt);
    border-color: rgba(255,159,67,.2);
    color: var(--mz-orange);
}

/* ── STAT CARDS ── */
.stat-card {
    background: var(--mz-card);
    border: 1px solid var(--mz-border);
    border-radius: 12px;
    padding: 1.2rem 1.3rem;
    position: relative; overflow: hidden;
    box-shadow: var(--mz-shadow);
    transition: transform .25s, box-shadow .25s;
    height: 100%;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--mz-shadow-h);
}
.glow-blob {
    position: absolute; top: -25px; right: -25px;
    width: 100px; height: 100px; border-radius: 50%;
    opacity: .07; pointer-events: none;
}
.stat-ico {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; margin-bottom: .85rem;
}
.s-purple .glow-blob { background: var(--mz-blue); }
.s-purple .stat-ico  { background: var(--mz-blue-lt); color: var(--mz-blue); }

.s-cyan .glow-blob   { background: var(--mz-teal); }
.s-cyan .stat-ico    { background: var(--mz-teal-lt); color: var(--mz-teal); }

.s-amber .glow-blob  { background: var(--mz-orange); }
.s-amber .stat-ico   { background: var(--mz-orange-lt); color: var(--mz-orange); }

.s-rose .glow-blob   { background: var(--mz-red); }
.s-rose .stat-ico    { background: var(--mz-red-lt); color: var(--mz-red); }

.stat-lbl {
    font-size: .68rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: .07em;
    color: var(--mz-muted); margin-bottom: .3rem;
}
.stat-val {
    font-size: 1.1rem; font-weight: 700;
    color: var(--mz-text); line-height: 1.15; word-break: break-all;
}
.stat-val.big { font-size: 1.8rem; }
.stat-sub { font-size: .7rem; color: var(--mz-muted); margin-top: .3rem; }
.stat-chip {
    display: inline-flex; align-items: center; gap: 3px;
    font-size: .68rem; font-weight: 600;
    padding: 2px 10px; border-radius: 20px; margin-top: .5rem;
}
.chip-g { background: var(--mz-green-lt);  color: var(--mz-green); }
.chip-c { background: var(--mz-teal-lt);   color: var(--mz-teal); }
.chip-a { background: var(--mz-orange-lt); color: var(--mz-orange); }
.chip-r { background: var(--mz-red-lt);    color: var(--mz-red); }

/* ── PANELS ── */
.panel {
    background: var(--mz-card);
    border: 1px solid var(--mz-border);
    border-radius: 12px;
    padding: 1.3rem 1.4rem;
    box-shadow: var(--mz-shadow);
    height: 100%;
}
.panel-dark {
    background: linear-gradient(135deg, var(--mz-blue) 0%, #2d47a8 100%);
    border-color: transparent;
    color: #fff;
}
.panel-dark .sec-title { color: rgba(255,255,255,.95); }
.panel-dark .sec-sub   { color: rgba(255,255,255,.6); }
.panel-dark .pb-lbl    { color: rgba(255,255,255,.6) !important; }
.panel-dark .pb-val    { color: #fff !important; }
.panel-dark .donut-pct { color: #fff; }
.panel-dark .donut-lbl { color: rgba(255,255,255,.6); }
.panel-dark .legend-item { color: rgba(255,255,255,.75); }
.panel-dark .pagu-box  {
    background: rgba(255,255,255,.1) !important;
    border-color: rgba(255,255,255,.15) !important;
}

.sec-title { font-size: .9rem; font-weight: 700; color: var(--mz-text); margin: 0 0 2px; }
.sec-sub   { font-size: .71rem; color: var(--mz-muted); margin: 0 0 1rem; }

/* ── DONUT ── */
.donut-wrap { position: relative; width: 145px; height: 145px; margin: .75rem auto; }
.donut-svg  { width: 100%; height: 100%; transform: rotate(-90deg); }
.donut-center {
    position: absolute; top: 50%; left: 50%;
    transform: translate(-50%, -50%); text-align: center;
}
.donut-pct { font-size: 1.7rem; font-weight: 700; color: var(--mz-text); line-height: 1; }
.donut-lbl { font-size: .63rem; color: var(--mz-muted); margin-top: 1px; }
.donut-legend { display: flex; gap: 1rem; justify-content: center; margin-top: .75rem; flex-wrap: wrap; }
.legend-item  { display: flex; align-items: center; gap: 5px; font-size: .73rem; color: var(--mz-muted); }
.legend-dot   { width: 8px; height: 8px; border-radius: 3px; }

/* ── PAGU BOX ── */
.pagu-box {
    background: var(--mz-card2);
    border: 1px solid var(--mz-border);
    border-radius: 10px; padding: .75rem .9rem;
    margin-top: .9rem; display: flex;
    justify-content: space-between; align-items: center;
}
.pagu-box .pb-lbl { font-size: .67rem; color: var(--mz-muted); }
.pagu-box .pb-val { font-size: .8rem; font-weight: 700; color: var(--mz-text); }

/* ── PROGRESS ── */
.prog-item { margin-bottom: .9rem; }
.prog-label {
    display: flex; justify-content: space-between;
    font-size: .73rem; margin-bottom: 5px; color: var(--mz-muted);
}
.prog-label strong { color: var(--mz-text); }
.prog-track { height: 7px; background: var(--mz-card3); border-radius: 10px; overflow: hidden; }
.prog-fill  { height: 100%; border-radius: 10px; transition: width 1.4s cubic-bezier(.4,0,.2,1); }

/* ── TABLE ── */
.spj-table { width: 100%; border-collapse: separate; border-spacing: 0 4px; }
.spj-table thead th {
    font-size: .67rem; text-transform: uppercase;
    letter-spacing: .07em; color: var(--mz-muted);
    padding: 0 10px 6px; border: none; background: transparent;
}
.spj-table tbody tr {
    background: var(--mz-card2);
    transition: background .18s;
}
.spj-table tbody tr:hover { background: var(--mz-blue-lt); }
.spj-table tbody td {
    padding: 9px 10px; font-size: .8rem;
    color: var(--mz-text); border: none; vertical-align: middle;
}
.spj-table tbody td:first-child { border-radius: 8px 0 0 8px; }
.spj-table tbody td:last-child  { border-radius: 0 8px 8px 0; }

.type-badge {
    display: inline-block; font-size: .66rem; font-weight: 700;
    padding: 2px 9px; border-radius: 20px;
}
.tb-ls  { background: var(--mz-blue-lt);   color: var(--mz-blue); }
.tb-gu  { background: var(--mz-teal-lt);   color: var(--mz-teal); }
.tb-tu  { background: var(--mz-orange-lt); color: var(--mz-orange); }
.tb-up  { background: var(--mz-red-lt);    color: var(--mz-red); }
.tb-def { background: var(--mz-card3);     color: var(--mz-muted); }
.mono   { font-size: .75rem; }

/* ── ANGGARAN LIST ── */
.ang-item {
    display: flex; align-items: center; gap: .75rem;
    background: var(--mz-card2);
    border: 1px solid var(--mz-border);
    border-radius: 10px; padding: .8rem .95rem;
    margin-bottom: .5rem; transition: all .2s;
}
.ang-item:hover {
    border-color: rgba(67,94,190,.3);
    background: var(--mz-blue-lt);
}
.ang-ico {
    width: 34px; height: 34px; border-radius: 9px;
    flex-shrink: 0; display: flex; align-items: center;
    justify-content: center; font-size: .95rem;
}
.ang-body  { flex: 1; min-width: 0; }
.ang-name  { font-size: .8rem; font-weight: 600; color: var(--mz-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ang-pagu  { font-size: .67rem; color: var(--mz-muted); }
.ang-pct   { font-size: .75rem; font-weight: 700; flex-shrink: 0; }
.ang-mini-track { height: 3px; background: var(--mz-border); border-radius: 5px; margin-top: 4px; overflow: hidden; }
.ang-mini-fill  { height: 100%; border-radius: 5px; }

/* ── TIMELINE ── */
.tl-item { display: flex; gap: .7rem; align-items: flex-start; margin-bottom: .85rem; }
.tl-dot  { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; margin-top: 4px; }
.c-purple { background: var(--mz-blue);   box-shadow: 0 0 6px rgba(67,94,190,.4); }
.c-cyan   { background: var(--mz-teal);   box-shadow: 0 0 6px rgba(0,184,217,.4); }
.c-amber  { background: var(--mz-orange); box-shadow: 0 0 6px rgba(255,159,67,.4); }
.c-rose   { background: var(--mz-red);    box-shadow: 0 0 6px rgba(234,84,85,.4); }
.tl-text  { font-size: .78rem; color: var(--mz-text); line-height: 1.4; }
.tl-time  { font-size: .68rem; color: var(--mz-muted); margin-top: 2px; }

/* ── ANIMATIONS ── */
@keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.fade-up { animation: fadeUp .45s ease both; }
.d1 { animation-delay: .04s; } .d2 { animation-delay: .09s; } .d3 { animation-delay: .13s; }
.d4 { animation-delay: .18s; } .d5 { animation-delay: .22s; } .d6 { animation-delay: .27s; } .d7 { animation-delay: .32s; }

::-webkit-scrollbar { width: 4px; height: 4px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: rgba(67,94,190,.25); border-radius: 4px; }
</style>

<div class="dash-wrap">

    {{-- HEADER --}}
    <div class="dash-header fade-up">
        <div class="dash-header-left">
            <h1>Selamat Datang, {{ $userx->fullname ?? 'User' }} &#128075;</h1>
            <p>Sistem Pertanggungjawaban Anggaran &nbsp;&middot;&nbsp; Role: {{ ucfirst($userx->role ?? '-') }}</p>
        </div>
        <div class="header-right">
            <span class="badge-pill tahun">TA {{ $tahun }}</span>
            <span class="badge-pill" id="live-date">--</span>
        </div>
    </div>

    {{-- ROW 1: STAT CARDS --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3 fade-up d1">
            <div class="stat-card s-purple">
                <div class="glow-blob"></div>
                <div class="stat-ico"><i class="iconly-boldDocument"></i></div>
                <div class="stat-lbl">Total SPJ LS</div>
                <div class="stat-val">Rp {{ number_format($total_ls, 0, ',', '.') }}</div>
                <div class="stat-sub">Langsung &mdash; Pihak Ketiga</div>
                <span class="stat-chip chip-g">&#9679; Langsung (LS)</span>
            </div>
        </div>
        <div class="col-6 col-xl-3 fade-up d2">
            <div class="stat-card s-cyan">
                <div class="glow-blob"></div>
                <div class="stat-ico"><i class="iconly-boldDocument"></i></div>
                <div class="stat-lbl">Total SPJ GU</div>
                <div class="stat-val">Rp {{ number_format($total_gu, 0, ',', '.') }}</div>
                <div class="stat-sub">Ganti Uang Persediaan</div>
                <span class="stat-chip chip-c">&#9679; Ganti Uang (GU)</span>
            </div>
        </div>
        <div class="col-6 col-xl-3 fade-up d3">
            <div class="stat-card s-amber">
                <div class="glow-blob"></div>
                <div class="stat-ico"><i class="iconly-boldChart"></i></div>
                <div class="stat-lbl">Total Keseluruhan</div>
                <div class="stat-val">Rp {{ number_format($total_spj_nilai, 0, ',', '.') }}</div>
                <div class="stat-sub">Semua Jenis SPJ</div>
                <span class="stat-chip chip-a">&#9679; Total Realisasi</span>
            </div>
        </div>
        <div class="col-6 col-xl-3 fade-up d4">
            <div class="stat-card s-rose">
                <div class="glow-blob"></div>
                <div class="stat-ico"><i class="iconly-boldDiscovery"></i></div>
                <div class="stat-lbl">Jumlah Dokumen SPJ</div>
                <div class="stat-val big">{{ $total_spj_count }}</div>
                <div class="stat-sub">Dokumen diproses TA {{ $tahun }}</div>
                <span class="stat-chip chip-r">&#9679; Dokumen SPJ</span>
            </div>
        </div>
    </div>

    {{-- ROW 2: CHART + DONUT + REALISASI --}}
    <div class="row g-3 mb-3">

        <div class="col-lg-5 fade-up d5">
            <div class="panel">
                <p class="sec-title">Tren Pengeluaran SPJ</p>
                <p class="sec-sub">Nilai per Bulan (Juta Rp) &mdash; TA {{ $tahun }}</p>
                <canvas id="trendChart" height="195"></canvas>
            </div>
        </div>

        <div class="col-lg-3 fade-up d5">
            <div class="panel panel-dark">
                <p class="sec-title">Komposisi SPJ</p>
                <p class="sec-sub">Rasio LS vs GU &mdash; TA {{ $tahun }}</p>
                <div class="donut-wrap">
                    <svg class="donut-svg" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="48" fill="none"
                            stroke="rgba(255,255,255,0.15)" stroke-width="13"/>
                        <circle cx="60" cy="60" r="48" fill="none"
                            stroke="#ffffff" stroke-width="13"
                            stroke-dasharray="{{ $dash_ls }} {{ $circum - $dash_ls }}"
                            stroke-linecap="round"/>
                        <circle cx="60" cy="60" r="48" fill="none"
                            stroke="rgba(255,255,255,0.4)" stroke-width="13"
                            stroke-dasharray="{{ $dash_gu }} {{ $circum - $dash_gu }}"
                            stroke-dashoffset="-{{ $dash_ls }}"
                            stroke-linecap="round"/>
                    </svg>
                    <div class="donut-center">
                        <div class="donut-pct">{{ $donut_ls_pct }}%</div>
                        <div class="donut-lbl">LS</div>
                    </div>
                </div>
                <div class="donut-legend">
                    <div class="legend-item">
                        <div class="legend-dot" style="background:#ffffff"></div>
                        LS &mdash; {{ $donut_ls_pct }}%
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot" style="background:rgba(255,255,255,.4)"></div>
                        GU &mdash; {{ $donut_gu_pct }}%
                    </div>
                </div>
                <div class="pagu-box">
                    <div>
                        <div class="pb-lbl">Pagu Anggaran</div>
                        <div class="pb-val">Rp {{ number_format($pagu_total, 0, ',', '.') }}</div>
                    </div>
                    <div style="text-align:right">
                        <div class="pb-lbl">Sisa Pagu</div>
                        <div class="pb-val" style="color:var(--amber)">
                            Rp {{ number_format($sisa_pagu_total, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 fade-up d6">
            <div class="panel">
                <p class="sec-title">Realisasi Anggaran</p>
                <p class="sec-sub">Penyerapan per sumber dana vs pagu</p>
                <div class="prog-item">
                    <div class="prog-label">
                        <span>Keseluruhan</span><strong>{{ $persen_realisasi }}%</strong>
                    </div>
                    <div class="prog-track">
                        <div class="prog-fill" style="width:0%;background:linear-gradient(90deg,#435ebe,#6b84d4)"
                             data-w="{{ $persen_realisasi }}"></div>
                    </div>
                </div>
                <div class="prog-item">
                    <div class="prog-label">
                        <span>SPJ Langsung (LS)</span><strong>{{ $realisasi_ls }}%</strong>
                    </div>
                    <div class="prog-track">
                        <div class="prog-fill" style="width:0%;background:linear-gradient(90deg,#00b8d9,#00d4f5)"
                             data-w="{{ $realisasi_ls }}"></div>
                    </div>
                </div>
                <div class="prog-item">
                    <div class="prog-label">
                        <span>Ganti Uang (GU)</span><strong>{{ $realisasi_gu }}%</strong>
                    </div>
                    <div class="prog-track">
                        <div class="prog-fill" style="width:0%;background:linear-gradient(90deg,#ff9f43,#ffbb75)"
                             data-w="{{ $realisasi_gu }}"></div>
                    </div>
                </div>
                <div class="pagu-box">
                    <div>
                        <div class="pb-lbl">Total Realisasi</div>
                        <div class="pb-val" style="color:#28c76f">
                            Rp {{ number_format($realisasi_total, 0, ',', '.') }}
                        </div>
                    </div>
                    <div style="text-align:right">
                        <div class="pb-lbl">Persentase</div>
                        <div class="pb-val" style="font-size:1.05rem;color:#435ebe">
                            {{ $persen_realisasi }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 3: TABLE + SIDE --}}
    <div class="row g-3">

        <div class="col-lg-8 fade-up d6">
            <div class="panel">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <p class="sec-title mb-0">SPJ Terbaru</p>
                        <p class="sec-sub mb-0">Dokumen SPJ yang baru diproses</p>
                    </div>
                    <a href="#" class="btn btn-sm"
                       style="background:rgba(139,124,248,.15);color:var(--purple);border:1px solid rgba(139,124,248,.25);border-radius:9px;font-size:.72rem;padding:4px 13px">
                        Lihat Semua &#8594;
                    </a>
                </div>
                <div style="overflow-x:auto">
                    <table class="spj-table">
                        <thead>
                            <tr>
                                <th>No. Kwitansi</th>
                                <th>Uraian</th>
                                <th>Sumber Dana</th>
                                <th>Total</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($recent_spj as $spj)
                            <tr>
                                <td class="mono">{{ $spj->nomor_kwitansi ?? $spj->nomor_spj ?? '-' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($spj->uraian ?? '-', 32) }}</td>
                                <td>
                                    @php
                                        $sd = strtoupper($spj->sumber_dana ?? '');
                                        if ($sd == 'LS') {
                                            $cls = 'tb-ls';
                                        } elseif ($sd == 'GU') {
                                            $cls = 'tb-gu';
                                        } elseif ($sd == 'TU') {
                                            $cls = 'tb-tu';
                                        } elseif ($sd == 'UP') {
                                            $cls = 'tb-up';
                                        } else {
                                            $cls = 'tb-def';
                                        }
                                    @endphp
                                    <span class="type-badge {{ $cls }}">{{ $sd ?: '-' }}</span>
                                </td>
                                <td class="mono">Rp {{ number_format($spj->total ?? 0, 0, ',', '.') }}</td>
                                <td style="font-size:.7rem;color:var(--muted)">
                                    {{ \Carbon\Carbon::parse($spj->tanggal)->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center;color:var(--muted);padding:2rem 0;font-size:.8rem">
                                    Belum ada data SPJ untuk tahun {{ $tahun }}.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">

            <div class="panel mb-3 fade-up d6" style="height:auto">
                <p class="sec-title">Anggaran / RKA OPD</p>
                <p class="sec-sub">Sub Kegiatan &mdash; serapan tertinggi</p>
                @php
                    $ang_clr = array('var(--purple)','var(--cyan)','var(--amber)','var(--rose)','var(--green)');
                @endphp
                @forelse($daftar_anggaran as $i => $ang)
                    @php
                        $pct = 0;
                        if ($ang->pagu_anggaran > 0) {
                            $pct = min(round(($ang->realisasi / $ang->pagu_anggaran) * 100), 100);
                        }
                        $clr = $ang_clr[$i % 5];
                        $namaSubkeg = 'Sub Kegiatan #' . ($i + 1);
                        if ($ang->subKegiatan) {
                            $namaSubkeg = $ang->subKegiatan->nama ?? $namaSubkeg;
                        }
                    @endphp
                    <div class="ang-item">
                        <div class="ang-ico" style="color:{{ $clr }}">
                            <i class="iconly-boldFolder"></i>
                        </div>
                        <div class="ang-body">
                            <div class="ang-name" title="{{ $namaSubkeg }}">{{ $namaSubkeg }}</div>
                            <div class="ang-pagu">Pagu: Rp {{ number_format($ang->pagu_anggaran ?? 0, 0, ',', '.') }}</div>
                            <div class="ang-mini-track">
                                <div class="ang-mini-fill" style="width:{{ $pct }}%;background:{{ $clr }}"></div>
                            </div>
                        </div>
                        <div class="ang-pct" style="color:{{ $clr }}">{{ $pct }}%</div>
                    </div>
                @empty
                    <p style="color:var(--muted);font-size:.79rem;text-align:center;padding:1rem 0">
                        Belum ada data anggaran untuk TA {{ $tahun }}.
                    </p>
                @endforelse
            </div>

            <div class="panel fade-up d7" style="height:auto">
                <p class="sec-title">Aktivitas Terkini</p>
                <p class="sec-sub">Log perubahan SPJ</p>
                @php
                    $dot_arr = array('c-purple','c-cyan','c-amber','c-rose','c-purple');
                @endphp
                @forelse($aktivitas as $i => $act)
                    @php
                        $tipe = $act['tipe'];
                        if ($tipe == 'ls') {
                            $dotCls = 'c-purple';
                        } elseif ($tipe == 'gu') {
                            $dotCls = 'c-cyan';
                        } elseif ($tipe == 'tu') {
                            $dotCls = 'c-amber';
                        } elseif ($tipe == 'up') {
                            $dotCls = 'c-rose';
                        } else {
                            $dotCls = $dot_arr[$i % 5];
                        }
                    @endphp
                    <div class="tl-item">
                        <div class="tl-dot {{ $dotCls }}"></div>
                        <div>
                            <div class="tl-text">{{ $act['text'] }}</div>
                            <div class="tl-time">{{ $act['waktu'] }}</div>
                        </div>
                    </div>
                @empty
                    <p style="color:var(--muted);font-size:.79rem">Belum ada aktivitas.</p>
                @endforelse
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function(){
    var d = new Date();
    var opts = { weekday:'short', day:'numeric', month:'short', year:'numeric' };
    document.getElementById('live-date').textContent = d.toLocaleDateString('id-ID', opts);
})();

window.addEventListener('load', function(){
    setTimeout(function(){
        var bars = document.querySelectorAll('.prog-fill[data-w]');
        for (var i = 0; i < bars.length; i++) {
            bars[i].style.width = bars[i].getAttribute('data-w') + '%';
        }
    }, 450);
});

(function(){
    var ctx  = document.getElementById('trendChart').getContext('2d');
    var gLS  = ctx.createLinearGradient(0, 0, 0, 220);
    gLS.addColorStop(0, 'rgba(67,94,190,0.2)');
    gLS.addColorStop(1, 'rgba(67,94,190,0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($bulan_labels),
            datasets: [
                {
                    label: 'SPJ LS',
                    data: @json($chart_ls),
                    borderColor: '#435ebe',
                    backgroundColor: gLS,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#435ebe',
                    pointRadius: 3.5,
                    pointHoverRadius: 6,
                    tension: 0.45,
                    fill: true
                },
                {
                    label: 'SPJ GU',
                    data: @json($chart_gu),
                    borderColor: '#00b8d9',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    pointBackgroundColor: '#00b8d9',
                    pointRadius: 3.5,
                    pointHoverRadius: 6,
                    tension: 0.45,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    labels: {
                        color: '#9e9e9e',
                        font: { size: 11 },
                        boxWidth: 9, boxHeight: 9, usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: '#ffffff',
                    borderColor: '#e7eaf0',
                    borderWidth: 1,
                    titleColor: '#404040',
                    bodyColor: '#9e9e9e',
                    padding: 10,
                    boxShadow: '0 4px 12px rgba(0,0,0,0.1)',
                    callbacks: {
                        label: function(c){
                            return ' Rp ' + c.parsed.y.toLocaleString('id-ID') + ' jt';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: '#f0f1f5' },
                    ticks: { color: '#9e9e9e', font: { size: 10 } },
                    border: { color: '#e7eaf0' }
                },
                y: {
                    grid: { color: '#f0f1f5' },
                    ticks: {
                        color: '#9e9e9e',
                        font: { size: 10 },
                        callback: function(v){ return v + ' jt'; }
                    },
                    border: { color: '#e7eaf0' }
                }
            }
        }
    });
})();
</script>
@endsection