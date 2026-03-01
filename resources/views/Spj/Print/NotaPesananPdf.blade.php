<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: "Book Antiqua"; font-size: 13px; }
    table { width:100%; border-collapse: collapse; }
    .center { text-align: center; }
    .judul { font-size: 16px; font-weight:bold; text-align:center; margin-top:10px; }
    th, td { padding: 6px; border:1px solid #000; }
    .noborder td { border:none !important; }
    .bold { font-weight: bold; }
    .ttd { width: 33%; height: 100px; vertical-align: bottom; }
    /* table, td {
        border: none solid #000;
    } */

     .td-right {
            text-align: right;
        }
</style>
</head>
<body>

<!-- HEADER -->
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

<div style="text-align:center; margin-top:15px;">
    <div style="font-size:15px; font-weight:bold;">NOTA PESANAN</div>
    <div style="margin-top:3px;">Nomor : {{ $spj->nomor_nota }}</div>
</div>

<br><br>
<table class="noborder">
    <tr>
        <td width="20%">Dari</td><td width="3%">:</td>
        <td>{{ $dari }}</td>
    </tr>
    <tr>
        <td>Jabatan</td><td>:</td>
        <td>{{ $jabatan }}</td>
    </tr>
    <tr>
        <td>Kepada</td><td>:</td>
        <td></td>
    </tr>
    <tr>
        <td>Dasar</td><td>:</td>
        <td>{{ $dasar }}</td>
    </tr>
    <tr>
        <td>Tanggal</td><td>:</td>
        <td>{{ \Carbon\Carbon::parse($spj->tanggal_nope)->translatedFormat('d F Y') }}</td>
    </tr>
</table>

<table style="width: 100%; border-collapse: none; margin-top: 10px;" class="noborder">
    <tr>
        <td style="width: 130px; font-weight: bold; vertical-align: top;">
            Isi Pesanan
        </td>
        <td style="width: 10px; vertical-align: top;">:</td>
        <td style="
            width:100%;
            white-space: normal;
            word-wrap: break-word;
            word-break: break-word;
            line-height: 1.5;
            text-align: justify;
        ">
            {{ $spj->uraian }}
        </td>
    </tr>
</table>

<br>
<!-- TABEL BARANG -->
<table>
    <tr>
        <th>No</th>
        <th>Nama Barang</th>
        <th>Volume</th>
        <th>Satuan</th>
        <th>Harga</th>
        <th>Jumlah</th>
    </tr>

    @foreach ($spj->details as $i => $d)
    <tr>
        <td class="center">{{ $i+1 }}</td>
        <td>{{ $d->nama_barang }}</td>
        <td class="center">{{ (int) $d->volume }}</td>
        <td class="center">{{ $d->satuan }}</td>
        <td class="td-right">{{ number_format($d->harga,0,',','.') }}</td>
        <td class="td-right">{{ number_format($d->jumlah,0,',','.') }}</td>
    </tr>
    @endforeach

    <tr>
        <td colspan="4" class="bold" style="text-align:left;">Terbilang : {{ $terbilang }}</td>
        <td colspan="1" class="td-right"><strong>Total</strong></td>
        <td class="td-right"><strong>{{ number_format($spj->total,0,',','.') }}</strong></td>
    </tr>
</table>

<br><br>

{{-- TTD --}}
<table class="noborder" width="100%">
    <tr class="center">
        <td>
            <br>
            <b>REKANAN</b>
        </td>
        <td>
            Palu, {{ \Carbon\Carbon::parse($spj->tanggal_nope)->translatedFormat('d F Y') }}<br>
            <b>PEJABAT PEMBUAT KOMITMEN</b>
        </td>
    </tr>

    <tr>
        <td class="ttd center bold">{{ $kepada }}</td>
        <td class="ttd center bold">{{ $ppk }}</td>
    </tr>

    <tr>
        <td class="center">{{ $namapertok }}</td>
        <td class="center">NIP. {{ $nip_ppk }}</td>
    </tr>
</table>

</body>
</html>
