@extends('Template.Layout')
@section('content')

<div class="page-heading">

    <div class="page-title">
        <h3>Dashboard PA / KPA</h3>
        <p class="text-subtitle text-muted">
            Selamat datang, {{ $userx->fullname }}
        </p>
    </div>

    <section class="section">

        <div class="row mb-3">

            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="fw-bold text-muted small">Menunggu Approval</div>
                        <h3 class="text-warning">{{ $stat['menunggu'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="fw-bold text-muted small">Disetujui</div>
                        <h3 class="text-success">{{ $stat['approved'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="fw-bold text-muted small">Ditolak</div>
                        <h3 class="text-danger">{{ $stat['ditolak'] }}</h3>
                    </div>
                </div>
            </div>

        </div>

        <div class="card shadow-sm border-0 mt-3">
            <div class="card-header bg-white">
                <h6 class="mb-0 text-warning">LPJ Menunggu Approval</h6>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nomor LPJ</th>
                            <th>Bendahara</th>
                            <th>PPK</th>
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
                            <td>{{ $lpj->ppk->fullname ?? '-' }}</td>
                            <td class="fw-bold text-primary">
                                Rp {{ number_format($lpj->total_spj,0,',','.') }}
                            </td>
                            <td>

                                <a href="{{ route('lpj.approval_pa.form', $lpj->id) }}" 
                                class="btn btn-sm btn-success">
                                    <i class="bi bi-check-circle"></i> Validasi
                                </a>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                Tidak ada LPJ menunggu approval 👍
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        <div class="card shadow-sm border-0 mt-3">
            <div class="card-header bg-white">
                <h6 class="mb-0 text-success">Riwayat LPJ Disetujui</h6>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nomor LPJ</th>
                            <th>Bendahara</th>
                            <th>PPK</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($lpjApproved as $i => $lpj)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td class="fw-bold">{{ $lpj->nomor_lpj }}</td>
                            <td>{{ $lpj->pembuat->fullname ?? '-' }}</td>
                            <td>{{ $lpj->ppk->fullname ?? '-' }}</td>
                            <td class="fw-bold text-success">
                                Rp {{ number_format($lpj->total_spj,0,',','.') }}
                            </td>
                            <td>
                                <span class="badge bg-light-success text-success">
                                    {{ strtoupper($lpj->status) }}
                                </span>
                            </td>
                            <td>

                                <form method="POST" action="{{ route('lpj.unapproval_pa', $lpj->id) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-danger">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">
                                Belum ada LPJ disetujui 👍
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
