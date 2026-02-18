<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: "Book Antiqua"; font-size: 13px; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #000; padding:5px; }

    .header-center { text-align:center; font-weight:bold; }
    .noborder td { border:none !important; }
    .center { text-align:center; }
    .bold { font-weight:bold; }
    .underline { text-decoration: underline; }
    .ttd-space { height:80px; }
</style>
</head>
<body>

<!-- ================= KOP =================== -->
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

<div class="header-center" style="margin-top:10px; font-size:15px;">
    BERITA ACARA PEMERIKSAAN BARANG<br>
    <span style="font-size:12px;">Nomor : {{ $nomor_bap }}</span>
</div>

<br>
<p style="text-align: justify;">
Pada hari ini {{ $tanggal }} bertempat di {{ ucwords(strtolower($unit->nama ?? '-')) }} berdasarkan 
Keputusan Kepala {{ ucwords(strtolower($unit->nama ?? '-')) }}  dengan Nomor: <b>{{ $nomor_bap }}</b>, 
yang bertanda tangan di bawah ini :
</p>

<!-- DATA PEJABAT -->
<table class="noborder" style="margin-top:10px;">
    <tr><td width="80">Nama</td><td width="10">:</td><td>{{ $ppk }}</td></tr>
    <tr><td>NIP</td><td>:</td><td>{{ $nip_ppk }}</td></tr>
    <tr><td>Jabatan</td><td>:</td><td>PEJABAT PEMBUAT KOMITMEN</td></tr>
</table>

<p style="text-align: justify;">
Berdasarkan surat permintaan dari penyedia tentang penyerahan hasil pekerjaan/
barang sesuai dengan Nota Pesanan / SPK / Surat Pesanan / Kontrak :
</p>

<table class="noborder">
    <tr><td width="80">Nomor</td><td width="10">:</td><td>{{ $nomor_np }}</td></tr>
    <tr><td>Tanggal</td><td>:</td><td>{{ $tanggal }}</td></tr>
</table>

<p>Telah melakukan pemeriksaan dengan hasil sebagai berikut :</p>

<br>
<!-- TABEL BARANG -->
<table>
    <tr class="center bold">
        <td width="30">No</td>
        <td>Nama Barang</td>
        <td width="70">Volume</td>
        <td width="70">Satuan</td>
        <td width="80">Harga</td>
        <td width="90">Jumlah</td>
    </tr>

    @foreach($spj->details as $i => $d)
    <tr>
        <td class="center">{{ $i+1 }}</td>
        <td>{{ $d->nama_barang }}</td>
        <td class="center">{{ rtrim(rtrim(number_format($d->volume,2,'.',''), '0'), '.') }}</td>
        <td class="center">{{ $d->satuan }}</td>
        <td style="text-align:right;">{{ number_format($d->harga,0,',','.') }}</td>
        <td style="text-align:right;">{{ number_format($d->jumlah,0,',','.') }}</td>
    </tr>
    @endforeach

    <tr>
        <td colspan="4" class="bold" style="text-align:center;">{{ $terbilang }}</td>
        <td colspan="1" class="bold" style="text-align:right;">Total Rp.</td>
        <td style="text-align:right;" class="bold">{{ number_format($total,0,',','.') }}</td>
    </tr>
</table>

{{-- <br> --}}
<p>Hasil pemeriksaan dinyatakan :</p>
<ol>
    <li>Baik</li>
    <li>Kurang Baik</li>
</ol>

<p>Demikian berita acara ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

<br>
<!-- TTD -->
@php
    $jumlahBaris = count($spj->details);
    $spacer = max(0, 1 - $jumlahBaris);   // Minimal butuh 12 baris agar tidak mepet
@endphp

@for ($i = 0; $i < $spacer; $i++)
    <div style="height:18px;"></div>
@endfor

<div style="page-break-inside: avoid;">
    <table class="noborder">
        <tr>
            <td style="width:50%; text-align:center; font-weight:bold;">
                REKANAN
            </td>
            <td style="width:50%; text-align:center; font-weight:bold;">
                YANG MEMERIKSA,<br>
                PEJABAT PEMBUAT KOMITMEN
            </td>
        </tr>

        <!-- Ruang tanda tangan fleksibel -->
        <tr>
            <td style="height:70px;"></td>
            <td></td>
        </tr>

        <tr>
            <!-- Rekanan -->
            <td style="text-align:center;">
                <u>{{ $spj->rekanan->nama_rekanan ?? '-' }}</u><br>
                {{ $spj->rekanan->npwp ?? '-' }}
            </td>

            <!-- PPK -->
            <td style="text-align:center;">
                <u>{{ $ppk }}</u><br>
                NIP. {{ $nip_ppk }}
            </td>
        </tr>
    </table>
</div>

</body>
</html>
