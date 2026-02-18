@extends('Template.Layout')
@section('content')

<div class="page-heading">

    <div class="page-title">
        <h3>Dashboard PPK</h3>
        <p class="text-subtitle text-muted">
            Selamat datang, {{ $userx->fullname }}
        </p>
    </div>

    <section class="section">

        {{-- STATISTIK --}}
        <div class="row mb-3">

            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="fw-bold text-muted small">Menunggu Verifikasi</div>
                        <h3 class="text-warning">{{ $stat['menunggu'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="fw-bold text-muted small">Sudah Disetujui</div>
                        <h3 class="text-success">{{ $stat['disetujui'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="fw-bold text-muted small">Dikembalikan</div>
                        <h3 class="text-danger">{{ $stat['revisi'] }}</h3>
                    </div>
                </div>
            </div>

        </div>

        {{-- DAFTAR LPJ --}}
        <div class="card shadow-sm border-0">

            <div class="card-header bg-white">
                <h6 class="mb-0">
                    LPJ Menunggu Verifikasi
                </h6>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nomor LPJ</th>
                            <th>Bendahara</th>
                            <th>Sub Kegiatan</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($lpjMenunggu as $i => $lpj)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td class="fw-bold">{{ $lpj->nomor_lpj }}</td>
                            <td>{{ $lpj->pembuat->fullname ?? '-' }}</td>
                            <td>
                                {{ \Illuminate\Support\Str::limit(
                                    $lpj->anggaran->subKegiatan->nama ?? '-', 35
                                ) }}
                            </td>
                            <td class="fw-bold text-primary">
                                Rp {{ number_format($lpj->total_spj,0,',','.') }}
                            </td>
                            <td>
                                @if($lpj->status == 'diajukan')
                                    {{-- ✅ BELUM DIVERIFIKASI --}}
                                    <a href="{{ route('lpj.verifikasi_ppk.form',$lpj->id) }}"
                                    class="btn btn-sm btn-warning">
                                        <i class="bi bi-check2-square"></i> Verifikasi
                                    </a>

                                @elseif($lpj->status == 'disetujui_ppk')

                                    {{-- ✅ SUDAH DIVERIFIKASI --}}
                                    <form action="{{ route('lpj.unverifikasi_ppk',$lpj->id) }}"
                                        method="POST" style="display:inline">
                                        @csrf
                                        <button class="btn btn-sm btn-danger">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                            Unverifikasi
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Tidak ada LPJ menunggu verifikasi 👍
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>

        </div>

    </section>
</div>

@endsection
