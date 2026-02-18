@extends('Template.Layout')
@section('content')

<div class="page-heading">

    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6">
                <h3>Buat LPJ Baru</h3>
                <p class="text-subtitle text-muted">Input Laporan Pertanggungjawaban</p>
            </div>
        </div>
    </div>

    <section class="section">

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('lpj.store') }}">
            @csrf

            <div class="row">

                {{-- PANEL KIRI --}}
                <div class="col-lg-4">

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Informasi LPJ</h6>
                        </div>

                        <div class="card-body">

                            {{-- Jenis --}}
                            <div class="mb-3">
                                <label class="form-label">Jenis</label>
                                <select name="jenis" class="form-select" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="GU">GU</option>
                                    <option value="LS">LS</option>
                                    <option value="TU">TU</option>
                                    <option value="UP">UP</option>
                                </select>
                            </div>

                            {{-- Bulan --}}
                            <div class="mb-3">
                                <label class="form-label">Periode Bulan</label>
                                <select name="periode_bulan" class="form-select" required>
                                    @foreach($bulanList as $key => $bulan)
                                        <option value="{{ $key }}">{{ $bulan }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tahun --}}
                            <div class="mb-3">
                                <label class="form-label">Tahun</label>
                                <input type="number"
                                       name="periode_tahun"
                                       class="form-control"
                                       value="{{ $tahun }}"
                                       readonly>
                            </div>

                            {{-- Sub Kegiatan --}}
                            <div class="mb-3">
                                <label class="form-label">Sub Kegiatan</label>
                                <select name="id_anggaran" class="form-select" required>
                                    <option value="">-- Pilih Sub Kegiatan --</option>
                                    @foreach($anggaranList as $anggaran)
                                        <option value="{{ $anggaran->id }}">
                                            {{ $anggaran->subKegiatan->nama ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Saldo Awal --}}
                            <div class="mb-3">
                                <label class="form-label">Saldo Awal</label>
                                <input type="number"
                                       name="saldo_awal"
                                       id="saldo_awal"
                                       class="form-control"
                                       required>
                            </div>

                            {{-- Total --}}
                            <div class="mb-3">
                                <label class="form-label">Estimasi Total SPJ</label>
                                <input type="text"
                                       id="total_spj"
                                       class="form-control fw-bold text-primary"
                                       value="Rp 0"
                                       readonly>
                            </div>

                            {{-- Sisa --}}
                            <div class="mb-3">
                                <label class="form-label">Estimasi Sisa Saldo</label>
                                <input type="text"
                                       id="saldo_sisa"
                                       class="form-control fw-bold"
                                       value="Rp 0"
                                       readonly>
                            </div>

                            {{-- Keterangan --}}
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-save"></i> Simpan LPJ
                            </button>

                        </div>
                    </div>

                </div>

                {{-- PANEL KANAN --}}
                <div class="col-lg-8">

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                Pilih SPJ
                                <span class="badge bg-primary">{{ $spjBelumLpj->count() }}</span>
                            </h6>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th></th>
                                        <th>No</th>
                                        <th>No Kwitansi</th>
                                        <th>Uraian</th>
                                        <th>Total</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>

                                <tbody>
                                @foreach($spjBelumLpj as $i => $spj)
                                    <tr>
                                        <td>
                                            <input type="checkbox"
                                                   name="spj_ids[]"
                                                   value="{{ $spj->id }}"
                                                   class="spj-checkbox"
                                                   data-total="{{ $spj->total }}">
                                        </td>
                                        <td>{{ $i+1 }}</td>
                                        <td>{{ $spj->nomor_kwitansi ?? '-' }}</td>
                                        <td>{{ $spj->uraian ?? '-' }}</td>
                                        <td class="fw-bold text-primary">
                                            Rp {{ number_format($spj->total,0,',','.') }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($spj->tanggal)->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>

                </div>

            </div>
        </form>

    </section>
</div>

{{-- 🔥 AUTO HITUNG TOTAL --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const checkboxes = document.querySelectorAll('.spj-checkbox');
    const totalSpjEl = document.getElementById('total_spj');
    const saldoAwal  = document.getElementById('saldo_awal');
    const saldoSisa  = document.getElementById('saldo_sisa');

    function hitung() {
        let total = 0;

        checkboxes.forEach(cb => {
            if (cb.checked) {
                total += parseFloat(cb.dataset.total);
            }
        });

        totalSpjEl.value = "Rp " + total.toLocaleString('id-ID');

        let saldo = parseFloat(saldoAwal.value || 0);
        let sisa  = saldo - total;

        saldoSisa.value = "Rp " + sisa.toLocaleString('id-ID');

        if (sisa < 0) {
            saldoSisa.classList.add('text-danger');
        } else {
            saldoSisa.classList.remove('text-danger');
        }
    }

    checkboxes.forEach(cb => cb.addEventListener('change', hitung));
    saldoAwal.addEventListener('input', hitung);
});
</script>

@endsection
