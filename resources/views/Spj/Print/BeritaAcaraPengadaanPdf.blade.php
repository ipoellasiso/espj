<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: "Book Antiqua"; font-size: 13px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border:1px solid #000; padding:6px; }
    .noborder td { border: none !important; }
    .judul { text-align:center; font-size:16px; font-weight:bold; margin-bottom:5px; }
    .subjudul { text-align:center; margin-bottom:20px; }
</style>
</head>

<body>
    <table width="100%" class="noborder">
    <tr>
        <!-- LOGO -->
        <td style="width: 60px; text-align: left; vertical-align: top;">
            <img src="{{ public_path('logo/palu.png') }}" style="width: 50px;">
        </td>

        <!-- TEKS HEADER -->
        <td style="text-align: center;">
            <div style="font-size: 18px; font-weight: bold; display:flex; align-items:center; margin-right:80px;">
                PEMERINTAH KOTA PALU
            </div>

            <div style="font-size: 18px; font-weight: bold; margin-top:2px; display:flex; align-items:center; margin-right:80px;">
                {{ strtoupper($unit->nama ?? '-') }}
            </div>

            <div style="font-size: 12px; margin-top:2px; display:flex; align-items:center; margin-right:80px;">
                {{ $unit->alamat ?? '' }}
            </div>
        </td>
    </tr>
</table>

<hr style="margin-top:10px; border: 1px solid #000;">

<div class="judul">BERITA ACARA PENGADAAN BARANG</div>
<div class="subjudul">Nomor : {{ $nomor_bapp }}</div>

<table class="noborder">
    <tr>
        <td width="80">Nama</td><td width="10">:</td>
        <td>{{ $unit->pejabatbarang }}</td>
    </tr>

    <tr>
        <td>NIP</td><td>:</td>
        <td>{{ $unit->nip_pejabatbarang }}</td>
    </tr>

    <tr>
        <td>Jabatan</td><td>:</td>
        <td>PEJABAT PENGADAAN BARANG</td>
    </tr>
</table>

<p style="text-align:justify;">
Berdasarkan Surat Keputusan Kepala {{ $unit->nama ?? '-' }} Nomor : {{ $sk_nomor ?? '-' }},
Tanggal {{ $sk_tanggal ?? '-' }} tentang Penunjukan Pejabat Pengadaan,
Tahun 2025, sesuai dengan Peraturan Presiden Republik Indonesia Nomor 16 Tahun 2018
tentang Pengadaan Barang/Jasa Pemerintah telah mengadakan barang/jasa
sesuai dengan ketentuan yang berlaku dan sesuai dengan Nota Pesanan/ SPK/ Surat Pesanan/ Kontrak :
</p>

<table class="noborder">
    <tr>
        <td width="80">Nomor</td><td width="10">:</td>
        <td>{{ $spj->nomor_nota }}</td>
    </tr>

    <tr>
        <td>Tanggal</td><td>:</td>
        <td>{{ \Carbon\Carbon::parse($spj->tanggal_nope)->translatedFormat('d F Y') }}</td>
    </tr>
</table>

<p>Dengan rincian sebagai berikut :</p>

<table>
    <thead>
        <tr>
            <th width="30">No</th>
            <th>Nama Barang</th>
            <th width="80">Volume</th>
            <th width="70">Satuan</th>
            <th width="90">Harga</th>
            <th width="100">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach($spj->details as $i => $d)
        <tr>
            <td style="text-align:center;">{{ $i+1 }}</td>
            <td>{{ $d->nama_barang }}</td>
            <td style="text-align:center;">{{ number_format($d->volume,0,',','.') }}</td>
            <td style="text-align:center;">{{ $d->satuan }}</td>
            <td style="text-align:right;">{{ number_format($d->harga,0,',','.') }}</td>
            <td style="text-align:right;">{{ number_format($d->jumlah,0,',','.') }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="4" style="text-align:center; font-weight:bold;">{{ ($terbilang) }}</td>
            <td colspan="1" style="text-align:center; font-weight:bold;">Total</td>
            <td style="text-align:right; font-weight:bold;">
                {{ number_format($spj->total,0,',','.') }}
            </td>
        </tr>
    </tbody>
</table>

<br><br>

<table class="noborder" width="100%" style="text-align: center;">
    <tr>
        <!-- Kolom kiri: Rekanan -->
        <td style="width: 50%; font-size: 14px; text-align:center;">
            <strong>REKANAN</strong>
            <br><br><br><br><br><br>
            <u>{{ $kepada }}</u>
            <br>
            {{ $namapertok }}
        </td>

        <!-- Kolom kanan: Pejabat Pengadaan -->
        <td style="width: 50%; font-size: 14px; text-align:center;">
            <strong>PEJABAT PENGADAAN</strong>
            <br><br><br><br><br><br>
            <span style="font-weight: bold; text-decoration: underline;">
                {{ $unit->pejabatbarang ?? 'Tidak Ditemukan' }}
            </span>
            <br>
            NIP. {{ $unit->nip_pejabatbarang ?? 'NIP ' }}
        </td>
    </tr>
</table>

</body>
</html>
