<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Times New Roman'; font-size: 12pt; }
        table { width: 100%; border-collapse: collapse; }
        .table-bordered td, .table-bordered th {
            border: 1px solid black; padding: 5px;
        }
        .ttd { margin-top: 40px; text-align: center; }
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

<div style="text-align:center; font-size:12pt; font-weight:bold; margin-top:10px;">
    <u>BERITA ACARA PENYERAHAN BARANG</u>
</div>

<div style="text-align:center; margin-top:5px;">
    Nomor : {{ $nomor_bast }}
</div>

<br><br>

<!-- PEMBUKA -->
<p style="text-align: justify;">
Pada hari ini <b>{{ $hari }}</b> tanggal <b>{{ $hariHuruf }}</b> bulan <b>{{ $bulanHuruf }}</b> tahun <b>{{ $tahunHuruf }}</b>, berdasarkan Berita Acara Pemeriksaan Barang :
</p>

<table>
    <tr><td width="80px">Nomor</td><td width="10px">:</td><td>{{ $nomor_bap }}</td></tr>
    <tr><td>Tanggal</td><td>:</td><td>{{ $tanggal }}</td></tr>
</table>

{{-- <p style="text-align: justify;">
Telah menerima hasil pekerjaan/barang dari:
</p>

<table>
    <tr><td width="80px">Rekanan</td><td width="10px">:</td><td>{{ $rekanan }}</td></tr>
</table>

<br> --}}

<p>Dengan ini telah melakukan serah terima hasil pekerjaan sesuai dengan di bawah ini : </p>

<!-- TABEL BARANG -->
<table class="table-bordered">
    <thead>
        <tr style="text-align:center; font-weight:bold;">
            <th width="30px">No</th>
            <th>Nama Barang</th>
            <th width="60px">Volume</th>
            <th width="60px">Satuan</th>
            <th width="100px">Harga</th>
            <th width="120px">Jumlah</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($spj->details as $i => $d)
        <tr>
            <td style="text-align:center;">{{ $i+1 }}</td>
            <td>{{ $d->nama_barang }}</td>
            <td style="text-align:center;">{{ rtrim(rtrim(number_format($d->volume,2,'.',''), '0'), '.') }}</td>
            <td style="text-align:center;">{{ $d->satuan }}</td>
            <td style="text-align:right;">{{ number_format($d->harga,0,',','.') }}</td>
            <td style="text-align:right;">{{ number_format($d->jumlah,0,',','.') }}</td>
        </tr>
        @endforeach
        <tr style="font-weight:bold;">
            <td colspan="4" style="text-align:center;">{{ $terbilang }}</td>
            <td colspan="1" style="text-align:right;">Total Rp.</td>
            <td style="text-align:right;">{{ number_format($total,0,',','.') }}</td>
        </tr>
    </tbody>
</table>

<p>Demikian berita acara ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

<br><br><br>

<!-- TTD -->
@php
    $jumlahBaris = count($spj->details);
    $spacer = max(0, 7 - $jumlahBaris);   // Minimal butuh 12 baris agar tidak mepet
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
                Diterima Oleh,<br>
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
