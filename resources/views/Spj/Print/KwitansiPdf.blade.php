<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body {
        font-family: "Book Antiqua";
        font-size: 16px;
        line-height: 1.35;
        margin: 25px 50px;             /* margin F4 lebih lebar kiri kanan */
    }

    table { width: 100%; border-collapse: collapse; }

    .judul {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin: 18px 0 22px;
    }

    .noborder td { border: none !important; }

    /* kolom label */
    .label {
        width: 25%;
        font-weight: bold;
        vertical-align: top;
        padding: 4px 0;
    }

    .colon {
        width: 10px;
        padding: 4px 0;
        vertical-align: top;
    }

    .value {
        vertical-align: top;
        padding: 4px 0;
        word-break: break-word;
    }

    /* uraian */
    .uraian {
        white-space: normal;
        word-break: break-word;
        line-height: 1.45;
    }

    /* TTD */
    .ttd-table {
        width: 100%;
        margin-top: 45px;
        text-align: center;
    }

    .ttd-space {
        height: 70px;     /* ruang tanda tangan untuk F4 */
    }

    .bold { font-weight: bold; }

    .spasi-atas td {
    padding-top: 15px;     /* tambahkan jarak vertical */
}

.ttd-table {
    width: 100%;
    font-size: 15px; /* 🔥 kecil tapi tetap terbaca */
}

.ttd-title {
    font-size: 15px; /* judul sedikit lebih besar */
    font-weight: bold;
}

.ttd-name {
    font-size: 15px; /* nama pejabat */
    font-weight: bold;
    text-decoration: underline;
}

.ttd-nip {
    font-size: 15x;
    margin-top: 2px;
}

.ttd-space {
    height: 70px; /* jarak tanda tangan */
}

</style>
</head>

<body>

<!-- HEADER -->
<table class="noborder">
    <tr>
        <td class="label">Tahun</td>
        <td class="colon">:</td>
        <td class="value">{{ date('Y', strtotime($spj->tanggal)) }}</td>
    </tr>

    <tr>
        <td class="label">Sumber Dana</td>
        <td class="colon">:</td>
        <td class="value">{{ $spj->sumber_dana ?? '-' }}</td>
    </tr>

    <tr>
        <td class="label">Program</td>
        <td class="colon">:</td>
        <td class="value" colspan="4">{{ $program }}</td>
    </tr>

    <tr>
        <td class="label">Kegiatan</td>
        <td class="colon">:</td>
        <td class="value" colspan="4">{{ $kegiatan }}</td>
    </tr>

    <tr>
        <td class="label">Sub Kegiatan</td>
        <td class="colon">:</td>
        <td class="value" colspan="4">{{ $sub }}</td>
    </tr>

    <tr>
        <td class="label">Rekening Belanja</td>
        <td class="colon">:</td>
        <td class="value" colspan="4">{{ $rekening }}</td>
    </tr>
</table>

<br>
<p class="judul">TANDA PENERIMAAN</p>

<br>
<!-- KWITANSI -->
<table class="noborder">

    <tr>
        <td class="label">Sudah Terima Dari</td>
        <td class="colon">:</td>
        <td class="value">Kepala {{ $nama }}</td>
    </tr>

    <tr class="spasi-atas">
        <td class="label">Terbilang</td>
        <td class="colon">:</td>
        <td class="value bold">{{ ($terbilang) }}</td>
    </tr>

    <tr class="spasi-atas">
        <td class="label" style="vertical-align: top;">Untuk Pembayaran</td>
        <td class="colon" style="vertical-align: top;">:</td>
        <td class="value">
            <div class="uraian">{{ $spj->uraian }}</div>
        </td>
    </tr>

    <tr class="spasi-atas">
        <td class="label">Uang Sebesar</td>
        <td class="colon">:</td>
        <td class="value bold" style="font-size: 16px; text-align:left;">
            Rp {{ number_format($jumlah, 0, ',', '.') }}
        </td>
    </tr>

</table>

<br><br>
<!-- ========================== -->
<!--        TTD BAGIAN ATAS     -->
<!-- ========================== -->
<table class="ttd-table" style="margin-top: 25px;">
    <tr>
        <td class="ttd-title" style="width:33%; text-align:center;">
            Menyetujui,<br>
            Pejabat Pelaksana Teknis Kegiatan
        </td>

        <td class="ttd-title" style="width:33%; text-align:center;">
            Yang Membayarkan,<br>
            Bendahara Pengeluaran
        </td>

        <td class="ttd-title" style="width:33%; text-align:center;">
            Palu, {{ \Carbon\Carbon::parse($spj->tanggal)->translatedFormat('d F Y') }}<br>
            Yang Menerima
        </td>
    </tr>

    <!-- Jarak tanda tangan -->
    <tr>
        <td class="ttd-space"></td>
        <td class="ttd-space"></td>
        <td class="ttd-space"></td>
    </tr>

    <tr>
        <td class="text-center">
            <div class="ttd-name">{{ $pptk }}</div>
            <div class="ttd-nip">NIP. {{ $nip_pptk }}</div>
        </td>

        <td class="text-center">
            <div class="ttd-name">{{ $bendahara }}</div>
            <div class="ttd-nip">NIP. {{ $nip_bendahara }}</div>
        </td>

        <td class="text-center">
            <div class="ttd-name">{{ $ttd_nama_penerima }}</div>
            <div class="ttd-nip">
                @if($ttd_npwp_penerima !== '-') 
                    {{ $ttd_npwp_penerima }}
                @else
                    ———
                @endif
            </div>
        </td>
    </tr>
</table>

<!-- ========================== -->
<!--   SPASI ANTARA BLOK TTD    -->
<!-- ========================== -->
<div class="ttd-space-section"></div>


<!-- ========================== -->
<!-- TTD PENGGUNA ANGGARAN (Naik) -->
<!-- ========================== -->
<table class="ttd-table">
    <tr>
        <td class="ttd-title">Mengetahui,<br>Pengguna Anggaran</td>
    </tr>

    <tr>
        <td style="height: 70px;"></td> <!-- Jarak tanda tangan -->
    </tr>

    <tr>
        <td>
            <u>{{ $kepala }}</u><br>
            NIP. {{ $nip_kepala }}
        </td>
    </tr>
</table>

</body>
</html>
