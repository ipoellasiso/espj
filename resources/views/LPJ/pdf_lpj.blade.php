<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #000; }

    .kop { width: 100%; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 12px; }
    .kop table { width: 100%; }
    .kop td.logo { width: 80px; vertical-align: middle; text-align: center; }
    .kop td.teks { vertical-align: middle; text-align: center; }
    .kop .instansi { font-size: 13pt; font-weight: bold; text-transform: uppercase; }
    .kop .alamat   { font-size: 9pt; }

    .judul { text-align: center; margin: 10px 0 6px; }
    .judul h2 { font-size: 13pt; font-weight: bold; text-transform: uppercase; }
    .judul p  { font-size: 10pt; }

    .info-table { width: 100%; margin-bottom: 10px; border-collapse: collapse; font-size: 10pt; }
    .info-table td { padding: 2px 4px; vertical-align: top; }

    .spj-table { width: 100%; border-collapse: collapse; font-size: 10pt; margin-bottom: 10px; }
    .spj-table th, .spj-table td { border: 1px solid #000; padding: 4px 6px; }
    .spj-table th { background: #e8e8e8; text-align: center; font-weight: bold; }
    .spj-table td.center { text-align: center; }
    .spj-table td.right  { text-align: right; }
    .spj-table tr.total  { font-weight: bold; }

    .rekap-table { width: 55%; border-collapse: collapse; font-size: 10pt; margin-left: auto; }
    .rekap-table td { padding: 3px 6px; }

    .pernyataan { border: 1px solid #000; padding: 8px 12px; font-size: 10pt; margin-bottom: 14px; }

    .ttd-section { margin-top: 25px; }
    .ttd-table { width: 100%; border-collapse: collapse; font-size: 10pt; }
    .ttd-table td { text-align: center; vertical-align: top; }
    .ttd-space { height: 60px; }
    .nama-ttd { border-top: 1px solid #000; font-weight: bold; }

    .footer-page { font-size: 8pt; text-align: right; margin-top: 10px; }

    @page { margin: 2cm 2cm 2cm 2.5cm; }
</style>
</head>
<body>

{{-- ================== KOP ================== --}}
<div class="kop">
    <table>
        <tr>
            <td class="logo">
                <div style="width:65px;height:65px;border:2px solid #555;border-radius:50%;text-align:center;line-height:65px;font-size:8pt">
                    LOGO
                </div>
            </td>
            <td class="teks">
                <div class="instansi">PEMERINTAH KOTA/KABUPATEN ...</div>
                <div class="instansi" style="font-size:12pt">
                    {{ optional($lpj->unit)->nama ?? 'NAMA SATUAN KERJA' }}
                </div>
                <div class="alamat">
                    Alamat: {{ $userx->alamat ?? '-' }}
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- ================== JUDUL ================== --}}
<div class="judul">
    <h2>LAPORAN PERTANGGUNGJAWABAN (LPJ)</h2>
    <h2>BENDAHARA PENGELUARAN</h2>
    <p>
        Jenis: {{ $lpj->jenis ?? '-' }} |
        Periode: {{ $lpj->nama_bulan ?? '-' }} {{ $lpj->periode_tahun ?? '-' }}
    </p>
</div>

{{-- ================== INFO ================== --}}
<table class="info-table">
    <tr>
        <td>Nomor LPJ</td><td>:</td>
        <td><strong>{{ $lpj->nomor_lpj ?? '-' }}</strong></td>
    </tr>
    <tr>
        <td>Nama Bendahara</td><td>:</td>
        <td>{{ optional($lpj->pembuat)->fullname ?? '-' }}</td>
    </tr>
    <tr>
        <td>Sub Kegiatan</td><td>:</td>
        <td>{{ optional(optional($lpj->anggaran)->subKegiatan)->nama ?? '-' }}</td>
    </tr>
</table>

{{-- ================== PERNYATAAN ================== --}}
<div class="pernyataan">
    Dengan ini menyatakan bahwa seluruh transaksi pengeluaran telah sesuai
    dengan ketentuan yang berlaku.
</div>

{{-- ================== TABEL SPJ ================== --}}
<table class="spj-table">
    <thead>
        <tr>
            <th>No</th>
            <th>No. Kwitansi</th>
            <th>Uraian</th>
            <th>Penerima</th>
            <th>Tanggal</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
    @php $no = 1; @endphp
    @foreach($lpj->spjList as $spj)
        <tr>
            <td class="center">{{ $no++ }}</td>
            <td class="center">{{ $spj->nomor_kwitansi ?? $spj->nomor_spj ?? '-' }}</td>
            <td>{{ $spj->uraian ?? '-' }}</td>
            <td>{{ $spj->nama_penerima ?? '-' }}</td>
            <td class="center">
                {{ $spj->tanggal ? \Carbon\Carbon::parse($spj->tanggal)->format('d-m-Y') : '-' }}
            </td>
            <td class="right">
                {{ number_format($spj->total ?? 0, 0, ',', '.') }}
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr class="total">
            <td colspan="5" class="right">JUMLAH</td>
            <td class="right">
                {{ number_format($lpj->total_spj ?? 0, 0, ',', '.') }}
            </td>
        </tr>
    </tfoot>
</table>

{{-- ================== REKAP ================== --}}
<table class="rekap-table">
    <tr>
        <td>Saldo Awal</td><td>:</td>
        <td class="right">Rp {{ number_format($lpj->saldo_awal ?? 0, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td>Total SPJ</td><td>:</td>
        <td class="right">Rp {{ number_format($lpj->total_spj ?? 0, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td><strong>Sisa Kas</strong></td><td>:</td>
        <td class="right"><strong>Rp {{ number_format($lpj->saldo_akhir ?? 0, 0, ',', '.') }}</strong></td>
    </tr>
</table>

{{-- ================== TTD ================== --}}
<div class="ttd-section">
    <table class="ttd-table">
        <tr>
            <td>
                PA/KPA
                <div class="ttd-space"></div>
                <div class="nama-ttd">{{ $userx->kepala ?? '-' }}</div>
            </td>
            <td>
                PPK
                <div class="ttd-space"></div>
                <div class="nama-ttd">{{ optional($lpj->ppk)->fullname ?? '-' }}</div>
            </td>
            <td>
                Bendahara
                <div class="ttd-space"></div>
                <div class="nama-ttd">{{ optional($lpj->pembuat)->fullname ?? '-' }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="footer-page">
    Dicetak: {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
