<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Daftar Penerima Honor + Pajak</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid black; padding: 6px; }
        th { background: #eee; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .noborder td { border: none !important; }
    </style>
</head>

<body>

    <h3 style="text-align:center;">DAFTAR PENERIMA HONOR / INSENTIF</h3>
    {{-- <h4 style="text-align:center;">(Dengan Pajak)</h4> --}}
    <h4 style="text-align:center;">Tahun Anggaran {{ date('Y') }}</h4>

    <br>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Penerima</th>
                <th>Jabatan</th>
                <th>Honor (Rp)</th>
                <th>Nama Bank</th>
                <th>No. Rek</th>
                <th>Nilai Pajak (Rp)</th>
                <th>Diterima (Rp)</th>
                <th>TTD</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($spj->daftarHonor as $i => $h)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $h->nama }}</td>
                <td>{{ $h->jabatan }}</td>
                <td class="text-right">{{ number_format($h->jumlah, 0, ',', '.') }}</td>
                <td class="text-center">{{ $h->nama_bank }}</td>
                <td class="text-center">{{ $h->no_rekening, }}</td>
                <td class="text-right">{{ number_format($h->nilai_pajak, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($h->diterima, 0, ',', '.') }}</td>
                <td class="text-right"></td>
            </tr>
            @endforeach

            <tr>
                <th colspan="2" class="text-right">TOTAL</th>
                <th></th>
                <th class="text-right">{{ number_format($totalHonor, 0, ',', '.') }}</th>
                <th></th>
                <th></th>
                <th class="text-right">{{ number_format($totalPajak, 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format($totalTerima, 0, ',', '.') }}</th>
                <th></th>
            </tr>
        </tbody>
    </table>

    <br>

    <p><strong>Terbilang:</strong> {{ $terbilang }}</p>

    <br><br><br>

    <table class="noborder" style="border:0; width:100%; margin-top:30px;">
        <tr>
            <td class="text-center">
                Mengetahui,<br>
                Pengguna Anggaran<br><br><br><br><br><br>
                <u>{{ $unit->kepala ?? '-' }}</u><br>
                NIP. {{ $unit->nip_kepala ?? '-' }}
            </td>

            <td class="text-center">
                Palu, {{ $tanggal }}<br>
                Pejabat Pelaksana Teknis Kegiatan<br><br><br><br><br><br>
                <u>{{ $spj->anggaran->pptk->nama ?? '-' }}</u><br>
                NIP. {{ $spj->anggaran->pptk->nip ?? '-' }}
            </td>
        </tr>
    </table>

</body>
</html>
