<?php

namespace App\Http\Controllers;

use App\Models\Spj;
use App\Models\SpjDetail;
use App\Models\Anggaran;
use App\Models\RincianAnggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\DokumenCounter;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class SpjController extends Controller
{
// === TAMPILKAN DAFTAR SPJ ===
public function index(Request $request)
{
    // Ambil user yang login
    $user = Auth::guard('web')->user();

    // Cegah akses tanpa login
    if (!$user) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }

    // 🔥 Ambil hanya RKA milik unit dari user yang login
    $anggaran = \App\Models\Anggaran::with('subKegiatan')
        ->where('id_unit', $user->id_unit) // ⬅ filter berdasarkan unit user
        ->where('tahun', $user->tahun) // 🔥
        ->get();

    // 🔥 Tambahkan ini
    $rekanan = \App\Models\Rekanan::where('id_unit', $user->id_unit)->get();

    // Data untuk view
    $data = [
        'title'          => 'Data SPJ',
        'active_laporan' => 'active',
        'breadcumd'      => 'Laporan',
        'breadcumd1'     => 'SPJ',
        'breadcumd2'     => 'Daftar',
        'userx'          => $user,
        'anggaran'       => $anggaran,
        'rekanan'        => $rekanan,
    ];

    // Jika request via AJAX (DataTables)
    if ($request->ajax()) {
        $spj = \App\Models\Spj::with('anggaran.subKegiatan')
            ->where('id_unit', $user->id_unit) // ⬅ filter juga data SPJ sesuai unit
            ->whereYear('tanggal', $user->tahun) // 🔥 FILTER TAHUN LOGIN
            ->orderBy('id', 'desc')
            ->get();

            // <a href="'.route('spj.cetak.resmi', $row->id).'" target="_blank" class="btn btn-sm btn-success">
            //     <i class="bi bi-printer"></i>
            // </a>

                // <a href="javascript:void(0)" data-id="'.$row->id.'" class="btn btn-sm btn-info lihatSpj">
                //     <i class="bi bi-eye"></i>
                // </a>

        return datatables()->of($spj)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {

                $menu = '';

                // ===== KWITANSI SELALU ADA =====
                $menu .= '
                    <li><a class="dropdown-item" href="'.route('spj.cetak.kwitansi.pdf', $row->id).'" target="_blank">
                        Kwitansi
                    </a></li>
                ';

                // ===== HONOR =====
                if ($row->jenis_kwitansi == 'honor_transport') {

                    $menu .= '
                        <li><a class="dropdown-item" href="'.route('spj.cetak.honor', $row->id).'" target="_blank">
                            Daftar Penerima
                        </a></li>
                    ';
                }

                // ===== BELANJA MODAL =====
                elseif ($row->jenis_kwitansi == 'belanja_modal') {

                    $menu .= '
                        <li><a class="dropdown-item" href="'.route('spj.cetak.ba_penyerahan', $row->id).'" target="_blank">
                            BA Serah Terima Barang
                        </a></li>
                    ';
                }

                // ===== ✅ GAJI (KWITANSI ONLY) =====
                elseif ($row->jenis_kwitansi == 'gaji') {

                    // ❌ Tidak tambah apa-apa
                    // Kwitansi saja sudah cukup
                }

                // ===== PIHAK KETIGA BIASA =====
                else {

                    $menu .= '
                        <li><a class="dropdown-item" href="'.route('spj.cetak.nota', $row->id).'" target="_blank">
                            Nota Pesanan
                        </a></li>

                        <li><a class="dropdown-item" href="'.route('spj.cetak.bapp', $row->id).'" target="_blank">
                            BA Pengadaan
                        </a></li>

                        <li><a class="dropdown-item" href="'.route('spj.cetak.bapb', $row->id).'" target="_blank">
                            BA Pemeriksaan
                        </a></li>

                        <li><a class="dropdown-item" href="'.route('spj.cetak.ba_penyerahan', $row->id).'" target="_blank">
                            BA Serah Terima
                        </a></li>

                        <li><a class="dropdown-item" href="'.route('spj.cetak.penerimaan', $row->id).'" target="_blank">
                            BA Penerimaan
                        </a></li>
                    ';
                }

                // ===== CEK APAKAH SPJ SUDAH MASUK LPJ =====
                if ($row->id_lpj) {

                    // 🔥 MODE TERKUNCI → CETAK SAJA
                    return '
                        <span class="badge bg-danger mb-1">Sudah LPJ</span>

                        <div class="btn-group">
                            <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-printer"></i>
                            </button>
                            <ul class="dropdown-menu">
                                '.$menu.'
                            </ul>
                        </div>
                    ';
                }

                // ===== MODE NORMAL → EDIT + HAPUS + CETAK =====
                return '
                    <button type="button" class="btn btn-sm btn-warning editSpjs" data-id="'.$row->id.'">
                        <i class="bi bi-pencil"></i>
                    </button>

                    <a href="javascript:void(0)" data-id="'.$row->id.'" class="btn btn-sm btn-danger hapusSpj">
                        <i class="bi bi-trash"></i>
                    </a>

                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-printer"></i>
                        </button>
                        <ul class="dropdown-menu">
                            '.$menu.'
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    // Kirim ke view
    return view('Spj.Index', $data);
}

// === FORM INPUT ===
public function create()
{
    $anggaran = Anggaran::with('subKegiatan')->get();
    return view('Spj.create', compact('anggaran'));
}

// === SIMPAN SPJ BARU ===
public function store(Request $request)
{
    DB::beginTransaction();

    try {

        // 🔒 CEK RKA SUDAH DIKUNCI (PER OPD)
        $lock = \App\Models\RkaLock::where('is_active', 1)
            ->where('id_unit', Auth::user()->id_unit)
            ->first();

        if (!$lock || $lock->is_locked == 0) {
            DB::rollBack(); // ⬅ WAJIB
            return response()->json([
                'success' => false,
                'message' => 'RKA belum dikunci. SPJ tidak dapat dibuat.'
            ], 403);
        }

        $request->validate([
            'tanggal' => 'required|date',
            'id_anggaran' => 'required|integer|exists:anggaran,id',
            'uraian' => 'required|string',
            'nama_barang' => 'required|array',
            'volume' => 'required|array',
            'harga' => 'required|array',
            'id_rincian_anggaran' => 'array', // penting supaya koneksi ke rincian jalan
        ]);

        // 🔹 Ambil user login dan relasi unit
        $user = \App\Models\UserModel::with('unit')->find(Auth::id());
        $tahun = date('Y', strtotime($request->tanggal));
        $unitCode = strtoupper($user->unit->ket ?? 'UNDEF');

        // 🔥 VALIDASI REKANAN SESUAI OPD LOGIN (ANTI INJECT)
        if ($request->id_rekanan) {

            $validRekanan = \App\Models\Rekanan::where('id_unit', $user->id_unit)
                ->where('id', $request->id_rekanan)
                ->exists();

            if (!$validRekanan) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Rekanan tidak valid untuk OPD ini'
                ], 403);
            }
        }

        // 🔹 Fungsi ambil nomor dokumen otomatis
        $getNomor = function ($jenis) use ($user, $tahun) {
            $counter = \App\Models\DokumenCounter::firstOrCreate(
                [
                    'id_unit' => $user->id_unit,
                    'tahun' => $tahun,
                    'jenis_dokumen' => $jenis
                ],
                ['nomor_terakhir' => 0]
            );

            $counter->nomor_terakhir += 1;
            $counter->save();

            return str_pad($counter->nomor_terakhir, 4, '0', STR_PAD_LEFT);
        };

        // 🔹 Generate nomor berdasarkan counter
        $noSpj   = $getNomor('SPJ');
        $noKw    = $getNomor('KW');
        $noNota  = $getNomor('NP');
        $noBapp  = $getNomor('BAPP');
        $noBapb  = $getNomor('BAPB');
        $noBast  = $getNomor('BAST');
        $noTerima = $getNomor('BA-TRM');

        // 🔹 Format akhir nomor
        $nomorSPJ  = "$noSpj/SPJ/$unitCode/$tahun";
        $nomorKW   = "$noKw/KW/$unitCode/$tahun";
        $nomorNota = "$noNota/NP/$unitCode/$tahun";
        $nomorBAPP = "$noBapp/BAPP/$unitCode/$tahun";
        $nomorBAPB = "$noBapb/BAPB/$unitCode/$tahun";
        $nomorBAST = "$noBast/BAST/$unitCode/$tahun";
        $nomorBATerima = "$noTerima/BA-TRM/$unitCode/$tahun";

        // 🔹 Hitung total SPJ
        $total = 0;
        foreach ($request->nama_barang as $i => $nama) {
            $volume = floatval($request->volume[$i] ?? 0);
            if ($volume <= 0) continue;   // ⛔ skip barang kosong
            $total += $volume * floatval($request->harga[$i] ?? 0);
        }

        // $jenisPajak = $request->jenis_pajak;
        // $nilaiPajak = floatval($request->nilai_pajak ?? 0);
        // $ebilling   = $request->ebilling;
        // $ntpn       = $request->ntpn;

        // 🔹 Simpan data SPJ utama
        $spj = \App\Models\Spj::create([
            'id_anggaran' => $request->id_anggaran,
            'nomor_spj' => $nomorSPJ,
            'tanggal' => $request->tanggal,
            'tanggal_nope' => $request->tanggal_nope,
            'uraian' => $request->uraian,
            'total' => $total,
            'id_unit' => $user->id_unit,
            'id_rekanan' => $request->id_rekanan,   // 🔥 FIX
            'nomor_kwitansi' => $nomorKW,
            'nomor_nota' => $nomorNota,
            'nomor_bapp' => $nomorBAPP,
            'nomor_bapb' => $nomorBAPB,
            'nomor_bast' => $nomorBAST,
            'nomor_ba_penerimaan' => $nomorBATerima,
            'sumber_dana' => $request->sumber_dana, // 🔥 INI WAJIB
            'jenis_kwitansi' => $request->jenis_kwitansi,

            // // 🔥 PAJAK MANUAL
            // 'jenis_pajak' => $jenisPajak,
            // 'nilai_pajak' => $nilaiPajak,
            // 'ebilling' => $ebilling,
            // 'ntpn' => $ntpn,
        ]);

        $pajakJenis  = $request->pajak_jenis ?? [];
        $pajakNilai  = $request->pajak_nilai ?? [];
        $pajakBilling = $request->pajak_ebilling ?? [];
        $pajakNtpn    = $request->pajak_ntpn ?? [];

        foreach ($pajakJenis as $i => $jenis) {

            if (!$jenis) continue;

            \App\Models\SpjPajak::create([
                'id_spj'      => $spj->id,
                'jenis_pajak' => $jenis,
                'nilai_pajak' => floatval($pajakNilai[$i] ?? 0),
                'ebilling'    => $pajakBilling[$i] ?? null,
                'ntpn'        => $pajakNtpn[$i] ?? null,
            ]);
        }

        foreach ($request->nama_barang as $i => $nama) {

            $volume = floatval($request->volume[$i] ?? 0);
            if ($volume <= 0) continue;   // ⛔ JANGAN SIMPAN BARANG VOLUME 0

            $harga  = floatval($request->harga[$i] ?? 0);
            $jumlah = $volume * $harga;
            $rincianId = $request->id_rincian_anggaran[$i] ?? null;

            // Simpan detail
            SpjDetail::create([
                'id_spj' => $spj->id,
                'id_rincian_anggaran' => $rincianId,
                'nama_barang' => $nama,
                'volume' => $volume,
                'satuan' => $request->satuan[$i] ?? '',
                'harga' => $harga,
                'jumlah' => $jumlah,
            ]);

            // Kurangi anggaran HANYA jika volume > 0
            if ($rincianId) {
                $rincian = RincianAnggaran::find($rincianId);
                if ($rincian) {
                    // Kurangi volume seperti biasa
                    $rincian->koefisien = max(0, $rincian->koefisien - $volume);

                    // Hitungan sisa jumlah harus dikurangi dari total, bukan volume * harga RKA
                    $nilai_pengeluaran = $volume * $harga;  // HARGA REAL SPJ

                    $rincian->jumlah = max(0, $rincian->jumlah - $nilai_pengeluaran);

                    // Harga RKA tidak berubah
                    $rincian->save();
                }
            }
        }

        if ($request->jenis_kwitansi == 'honor_transport') {

            $honor_nama    = $request->honor_nama ?? [];
            $honor_jabatan = $request->honor_jabatan ?? [];
            $honor_jumlah  = $request->honor_jumlah ?? [];
            $honor_pajak   = $request->honor_pajak ?? [];

            $honor_bank      = $request->honor_bank ?? [];
            $honor_rekening  = $request->honor_rekening ?? [];
            $honor_ttd       = $request->honor_ttd ?? [];

            foreach ($honor_nama as $i => $nama) {

                $jumlah = floatval($honor_jumlah[$i] ?? 0);
                $pajak  = floatval($honor_pajak[$i] ?? 0);

                // Rumus pajak
                $nilai_pajak = $jumlah * $pajak / 100;
                $diterima    = $jumlah - $nilai_pajak;

                if (trim($nama) != '') {
                    \App\Models\DaftarPenerimaHonor::create([
                        'id_spj'      => $spj->id,
                        'nama'        => $nama,
                        'jabatan'     => $honor_jabatan[$i] ?? '',
                        'jumlah'      => $jumlah,
                        'pajak'       => $pajak,
                        'nilai_pajak' => $nilai_pajak,
                        'diterima'    => $diterima,

                        // ✅ BARU
                        'nama_bank'   => $honor_bank[$i] ?? null,
                        'no_rekening' => $honor_rekening[$i] ?? null,
                        'ttd'         => isset($honor_ttd[$i]) ? 1 : 0,
                    ]);
                }
            }
        }   

        // 🔹 Update realisasi dan sisa pagu di tabel Anggaran
        $anggaran = \App\Models\Anggaran::find($request->id_anggaran);
        if ($anggaran) {
            $realisasiBaru = $anggaran->realisasi + $total;
            $sisaPaguBaru = $anggaran->pagu_anggaran - $realisasiBaru;

            $anggaran->update([
                'realisasi' => $realisasiBaru,
                'sisa_pagu' => $sisaPaguBaru,
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => "SPJ berhasil disimpan dengan nomor otomatis: $nomorSPJ"
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}

// === CETAK LAPORAN ===
public function print($id)
{
    $spj = Spj::with(['anggaran', 'details'])->findOrFail($id);
    return view('Spj.print', compact('spj'));
}

public function getRincian($id)
{
    try {
        $rincian = \App\Models\RincianAnggaran::where('id_anggaran', $id)->get();

        if ($rincian->count() == 0) {
            return response()->json(['success' => false, 'message' => 'Tidak ada rincian untuk RKA ini']);
        }

    return response()->json([
            'success' => true,
            'data' => $rincian->map(function ($r) {
                return [
                    'id' => $r->id,
                    'nama_barang' => $r->uraian,
                    'satuan' => $r->satuan,
                    'harga' => $r->harga,
                    'volume' => $r->koefisien,
                    'jumlah' => $r->koefisien * $r->harga,
                    'sisa' => $r->koefisien // Tambahan ini
                ];
            })
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

public function destroy($id)
{
    DB::beginTransaction();

    try {
        // Ambil data SPJ utama
        $spj = \App\Models\Spj::findOrFail($id);
        $anggaran = \App\Models\Anggaran::find($spj->id_anggaran);

        // Ambil semua detail
        $details = \App\Models\SpjDetail::where('id_spj', $id)->get();

        // 🔁 KEMBALIKAN VOLUME & JUMLAH RKA SECARA BENAR
        foreach ($details as $detail) {

            if ($detail->id_rincian_anggaran) {
                $rincian = \App\Models\RincianAnggaran::find($detail->id_rincian_anggaran);

                if ($rincian) {

                    // 1️⃣ Kembalikan volume
                    $rincian->koefisien += $detail->volume;

                    // 2️⃣ Kembalikan jumlah berdasarkan HARGA REAL SPJ
                    $nilai_real = $detail->volume * $detail->harga;
                    $rincian->jumlah += $nilai_real;

                    // Harga RKA tetap — jangan ubah
                    $rincian->save();
                }
            }
        }

        // 🔁 Update total realisasi di tabel anggaran
        if ($anggaran) {
            $anggaran->realisasi = max(0, $anggaran->realisasi - $spj->total);
            $anggaran->sisa_pagu = $anggaran->pagu_anggaran - $anggaran->realisasi;
            $anggaran->save();
        }

        // 🔥 Hapus detail dan SPJ utama
        \App\Models\SpjDetail::where('id_spj', $id)->delete();
        $spj->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Data SPJ berhasil dihapus dan anggaran dikembalikan.',
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus SPJ: ' . $e->getMessage(),
        ]);
    }
}

// ✅ Ambil rekening berdasarkan RKA
public function getRekening($id_anggaran)
{
    try {

        $rincian = RincianAnggaran::where('id_anggaran', $id_anggaran)

            ->select(
                'kode_rekening',
                DB::raw("(SELECT uraian 
                          FROM sub_rincian_objek 
                          WHERE sub_rincian_objek.kode = rincian_anggaran.kode_rekening 
                          LIMIT 1) as nama_rekening")
            )

            ->distinct()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rincian
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

// ✅ Ambil rincian barang berdasarkan rekening tertentu
public function getRincianselect2($id_anggaran, $id_rekening)
{
    try {
        $rincian = \App\Models\RincianAnggaran::where('id_anggaran', $id_anggaran)
            ->where('kode_rekening', $id_rekening)
            ->get();

        if ($rincian->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada rincian untuk rekening ini.'
            ]);
        }

        // 🔹 Mapping sesuai kolom tabel abang
        return response()->json([
            'success' => true,
            'data' => $rincian->map(function ($r) {
                return [
                    'id' => $r->id,
                    'nama_barang' => $r->uraian ?? '-',              // dari kolom 'uraian'
                    'volume' => $r->koefisien ?? 0,                // dari kolom 'koefisien'
                    'satuan' => $r->satuan ?? '-',                 // dari kolom 'satuan'
                    'harga' => $r->harga ?? 0,                     // dari kolom 'harga'
                    'jumlah' => ($r->koefisien ?? 0) * ($r->harga ?? 0),
                    'sisa' => $r->koefisien ?? 0,                  // untuk validasi volume sisa
                ];
            }),
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        ]);
    }
}


public function getNextNumbers(Request $request)
{
    try {
        $user = \App\Models\UserModel::with('unit')->find(Auth::id());
        $tahun = $request->tahun ?? date('Y');
        $unitCode = strtoupper($user->unit->ket ?? 'UNDEF');

        $jenisList = ['SPJ', 'KW', 'NP', 'BAPP', 'BAPB', 'BAST', 'BA-TRM'];
        $results = [];

        foreach ($jenisList as $jenis) {
            $counter = \App\Models\DokumenCounter::firstOrCreate(
                [
                    'id_unit' => $user->id_unit,
                    'tahun' => $tahun,
                    'jenis_dokumen' => $jenis,
                ],
                ['nomor_terakhir' => 0]
            );

            $nextNomor = str_pad($counter->nomor_terakhir + 1, 4, '0', STR_PAD_LEFT);
            $results[$jenis] = "$nextNomor/$jenis/$unitCode/$tahun";
        }

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil nomor dokumen: ' . $e->getMessage(),
        ]);
    }
}

public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {
        $request->validate([
            'tanggal' => 'required|date',
            'id_anggaran' => 'required|integer|exists:anggaran,id',
            'uraian' => 'required|string',
            'nama_barang' => 'required|array',
            'volume' => 'required|array',
            'harga' => 'required|array',
            'id_rincian_anggaran' => 'array',
        ]);

        $spj = \App\Models\Spj::findOrFail($id);
        $user = \App\Models\UserModel::with('unit')->find(Auth::id());

        // 🔹 Ambil semua detail lama (untuk balikin volume sebelumnya)
        $detailsLama = \App\Models\SpjDetail::where('id_spj', $spj->id)->get();

        foreach ($detailsLama as $lama) {
            if ($lama->id_rincian_anggaran) {

                $rincian = RincianAnggaran::find($lama->id_rincian_anggaran);

                if ($rincian) {

                    // ✅ Kembalikan volume
                    $rincian->koefisien += $lama->volume;

                    // ✅ Kembalikan jumlah REAL lama (harga SPJ lama)
                    $nilai_real_lama = $lama->volume * $lama->harga;
                    $rincian->jumlah += $nilai_real_lama;

                    // 🚫 Jangan hitung ulang pakai harga RKA
                    $rincian->save();
                }
            }
        }

        // 🔹 Hapus semua detail lama SPJ (akan diganti yang baru)
        \App\Models\SpjDetail::where('id_spj', $spj->id)->delete();

        // 🔹 Hitung total baru
        $totalBaru = 0;
        foreach ($request->nama_barang as $i => $nama) {
            $totalBaru += ($request->volume[$i] ?? 0) * ($request->harga[$i] ?? 0);
        }

        // 🔹 Update data utama SPJ
        $spj->update([
            'tanggal' => $request->tanggal,
            'uraian' => $request->uraian,
            'total' => $totalBaru,
            'id_rekanan' => $request->id_rekanan,   // 🔥 FIX
            'sumber_dana' => $request->sumber_dana,   // 🔥 FIX
        ]);

        // 🔹 Tambah detail baru & kurangi lagi rincian_anggaran
        foreach ($request->nama_barang as $i => $nama) {
            $volume = $request->volume[$i] ?? 0;
            $harga = $request->harga[$i] ?? 0;
            $jumlah = $volume * $harga;
            $rincianId = $request->id_rincian_anggaran[$i] ?? null;

            $jenisPajak = $request->jenis_pajak[$i] ?? null;
            $nilaiPajak = floatval($request->nilai_pajak[$i] ?? 0);
            $ebilling   = $request->ebilling[$i] ?? null;
            $ntpn       = $request->ntpn[$i] ?? null;

            \App\Models\SpjDetail::create([
                'id_spj' => $spj->id,
                'id_rincian_anggaran' => $rincianId,
                'nama_barang' => $nama,
                'volume' => $volume,
                'satuan' => $request->satuan[$i] ?? '',
                'harga' => $harga,
                'jumlah' => $jumlah,

                // 🔥 PAJAK MANUAL
                'jenis_pajak' => $jenisPajak,
                'nilai_pajak' => $nilaiPajak,
                'ebilling' => $ebilling,
                'ntpn' => $ntpn,
            ]);

            if ($rincianId) {
                $rincian = \App\Models\RincianAnggaran::find($rincianId);
                if ($rincian) {
                    // ✅ Kurangi volume
                    $rincian->koefisien = max(0, $rincian->koefisien - $volume);

                    // ✅ Kurangi jumlah REAL SPJ
                    $nilai_real = $volume * $harga;
                    $rincian->jumlah = max(0, $rincian->jumlah - $nilai_real);

                    $rincian->save();
                }
            }
        }

        // 🔹 Update total realisasi & sisa pagu di tabel Anggaran
        $anggaran = \App\Models\Anggaran::find($spj->id_anggaran);
        if ($anggaran) {
            $totalLama = $spj->total ?? 0;
            $realisasiBaru = max(0, $anggaran->realisasi - $totalLama + $totalBaru);
            $sisaPaguBaru = $anggaran->pagu_anggaran - $realisasiBaru;

            $anggaran->update([
                'realisasi' => $realisasiBaru,
                'sisa_pagu' => $sisaPaguBaru,
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'SPJ berhasil diperbarui dan rincian anggaran disesuaikan.',
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Gagal memperbarui SPJ: ' . $e->getMessage(),
        ]);
    }
}

public function edit($id)
{
    try {
        $spj = Spj::with(['details', 'anggaran.subKegiatan'])->findOrFail($id);

        // ambil rekening dari Rincian Anggaran
        $rekening = RincianAnggaran::where('id_anggaran', $spj->id_anggaran)
            ->select('kode_rekening')
            ->distinct()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $spj,
            'rekening' => $rekening
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

public function cetakKwitansiPdf($id)
{
    // $spj = Spj::with([
    //     'anggaran.subKegiatan.kegiatan.program',
    //     'details.rincian'
    // ])->findOrFail($id);

    $spj = Spj::with([
        'anggaran.subKegiatan.kegiatan.program',
        'anggaran.pptk', // <-- tambahkan ini agar PPTK ikut di-load
        'details.rincian.subRincianObjek',
        'rekanan'   // ← tambahkan ini
    ])->findOrFail($id);

    $user = \App\Models\UserModel::with('unit')->find(Auth::id());
    $unit = $user->unit;

    $jumlah = $spj->total;
    $terbilang = ucwords(strtolower($this->terbilang($jumlah))) . " Rupiah";

    // --------- HEADER KWITANSI ---------
    $sub       = $spj->anggaran->subKegiatan->nama ?? '-';
    $kegiatan  = $spj->anggaran->subKegiatan->kegiatan->nama ?? '-';
    $program   = $spj->anggaran->subKegiatan->kegiatan->program->nama ?? '-';

    // // rekening
    // $rekening  = optional(optional($spj->details->first())->rincian)->kode_rekening ?? '-';
    // $nama_belanja  = optional(optional($spj->details->first())->subRincianObjek)->uraian ?? '-';

    // $rekening_belanja = $rekening . ' - ' . $nama_belanja;

    // ==== REKENING BELANJA (BENAR) ====
    $firstDetail = $spj->details->first();

    $rekening = $firstDetail->rincian->kode_rekening ?? '-';

    // --- QUERY MANUAL cari nama belanja ---
    $namaBelanja = DB::table('sub_rincian_objek')
                    ->where('kode', $rekening)
                    ->value('uraian');

    $rekening_belanja = $rekening . ' - ' . ($namaBelanja ?? '-');

    // penerima
    // $penerima       = $spj->penerima ?? $unit->kepala ?? '-';
    // $nip_penerima   = $spj->nip_penerima ?? $unit->nip_kepala ?? '-';

    if ($spj->id_rekanan && $spj->rekanan) {
        // Ambil dari tabel rekanan
        $ttd_nama_penerima = $spj->rekanan->nama_rekanan;
        $ttd_npwp_penerima  = $spj->rekanan->npwp ?? '-';
    } else {
        // Default: gunakan data yang sudah ada
        $ttd_nama_penerima = $spj->nama_penerima ?? '-';
        $ttd_npwp_penerima  = $spj->nip_penerima ?? '-';
    }

    // kepala OPD
    $kepala       = $unit->kepala ?? '-';
    $nip_kepala   = $unit->nip_kepala ?? '-';

    // ========= PPTK BERDASARKAN ANGGARAN =========
    $pptkModel = optional($spj->anggaran->pptk);
    $pptk      = $pptkModel->nama ?? '-';
    $nip_pptk  = $pptkModel->nip ?? '-';

    // bendahara
    $bendahara      = $unit->bendahara ?? '-';
    $nip_bendahara  = $unit->nip_bendahara ?? '-';

    // bendahara
    $nama      = $unit->nama ?? '-';

    // Pengguna Anggaran (biasanya Kepala OPD)
    $pengguna_anggaran = $unit->pengguna_anggaran ?? $unit->kepala ?? '-';
    $nip_pengguna_anggaran = $unit->nip_pengguna_anggaran ?? $unit->nip_kepala ?? '-';

    // ======================================================
    //          FIX WRAP "UNTUK PEMBAYARAN" 100%
    // ======================================================
    $uraianPembayaran = $spj->uraian;
    $nama_penerima = $spj->nama_penerima;

    // Pecah jika ada kata > 20 karakter (DOMPDF tidak bisa justify tanpa ini)
    $uraianPembayaran = preg_replace_callback('/\S{20,}/', function ($match) {
        return chunk_split($match[0], 20, ' ');
    }, $uraianPembayaran);

    if ($spj->jenis_kwitansi == 'gaji') {

        $view = 'Spj.Print.KwitansiGajiPdf';   // 🔥 VIEW KHUSUS GAJI

    } else {

        $view = 'Spj.Print.KwitansiPdf';       // VIEW LAMA
    }

    // ======================================================

    // Kirim ke PDF
    $pdf = Pdf::loadView($view, [
        'spj' => $spj,
        'jumlah' => $jumlah,
        'terbilang' => $terbilang,
        'program' => $program,
        'kegiatan' => $kegiatan,
        'sub' => $sub,
        // 'rekening' => $rekening,
        'kepala' => $kepala,
        'nip_kepala' => $nip_kepala,
        'pptk' => $pptk,
        'nip_pptk' => $nip_pptk,
        'bendahara' => $bendahara,
        'nip_bendahara' => $nip_bendahara,
        // 'penerima' => $penerima,
        // 'nip_penerima' => $nip_penerima,
        'uraianPembayaran' => $uraianPembayaran,
        'nama' => $nama,
        'rekening' => $rekening_belanja,
        'nama_penerima' => $nama_penerima,
        'pengguna_anggaran' => $pengguna_anggaran,
        'nip_pengguna_anggaran' => $nip_pengguna_anggaran,
        'ttd_nama_penerima' => $ttd_nama_penerima,
        'ttd_npwp_penerima'  => $ttd_npwp_penerima,
    ])->setPaper([0, 0, 595.28, 935.43]); // F4 size mm -> pt

    $namaFile = 'kwitansi-' . str_replace(['/', '\\'], '-', $spj->nomor_spj) . '.pdf';

    return $pdf->stream($namaFile);
}

public function cetakNota($id)
{
    $spj = Spj::with([
        'anggaran.subKegiatan.kegiatan.program',
        'anggaran.pptk',
        'details',
        'rekanan'
    ])->findOrFail($id);

    $user = \App\Models\UserModel::with('unit')->find(Auth::id());
    $unit = $user->unit; // 🔥 Ambil unit organisasi yang benar

    $ppk  = $unit->ppk ?? '-';
    $nip_ppk = $unit->nip_ppk ?? '-';

    $dari = $unit->kepala ?? '-';
    $jabatan = "Kepala " . ($unit->nama ?? '-');

    $kepada = $spj->rekanan->nama_rekanan ?? '-';
    $namapertok = $spj->rekanan->npwp ?? '-';

    // Format dasar DPPA
    $kode = $spj->anggaran->subKegiatan->kode;
    $tahun = date('Y', strtotime($spj->tanggal));
    $dasar = "Nomor DPPA/{$kode}/001/{$tahun}";

    $jumlah = $spj->total;
    $terbilang = ucwords(strtolower($this->terbilang($jumlah))) . " Rupiah";

    $pdf = PDF::loadView('Spj.Print.NotaPesananPdf', [
        'spj' => $spj,
        'unit' => $unit,   // 🔥 kirim data unit organisasi yang benar
        'kepada' => $kepada,
        'ppk' => $ppk,
        'nip_ppk' => $nip_ppk,
        'dari' => $dari,
        'jabatan' => $jabatan,
        'dasar' => $dasar,
        'namapertok' => $namapertok,
        'terbilang' => $terbilang,
    ])->setPaper([0, 0, 595.28, 935.43], 'portrait');

    return $pdf->stream("nota-pesanan.pdf");
}

private function terbilang($angka)
{
    $angka = abs($angka);
    $baca = [
        "", "satu", "dua", "tiga", "empat", "lima",
        "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"
    ];

    if ($angka < 12) {
        return $baca[$angka];
    } elseif ($angka < 20) {
        return $this->terbilang($angka - 10) . " belas";
    } elseif ($angka < 100) {
        return $this->terbilang($angka / 10) . " puluh " . $this->terbilang($angka % 10);
    } elseif ($angka < 200) {
        return "seratus " . $this->terbilang($angka - 100);
    } elseif ($angka < 1000) {
        return $this->terbilang($angka / 100) . " ratus " . $this->terbilang($angka % 100);
    } elseif ($angka < 2000) {
        return "seribu " . $this->terbilang($angka - 1000);
    } elseif ($angka < 1000000) {
        return $this->terbilang($angka / 1000) . " ribu " . $this->terbilang($angka % 1000);
    } elseif ($angka < 1000000000) {
        return $this->terbilang($angka / 1000000) . " juta " . $this->terbilang($angka % 1000000);
    } else {
        return "terlalu besar";
    }
}

public function cetakBeritaAcara($id)
{
    $spj = Spj::with([
        'anggaran.subKegiatan.kegiatan.program',
        'anggaran.pptk',
        'details',
        'rekanan'
    ])->findOrFail($id);

    $user = \App\Models\UserModel::with('unit')->find(Auth::id());
    $unit = $user->unit;

    $jumlah = $spj->total;
    $terbilang = ucwords(strtolower($this->terbilang($jumlah))) . " Rupiah";

    // Kepala OPD
    $nama_kepala = $unit->kepala ?? '-';
    $nip_kepala = $unit->nip_kepala ?? '-';

    // PPTK
    $pptk = $spj->anggaran->pptk->nama ?? '-';
    $nip_pptk = $spj->anggaran->pptk->nip ?? '-';

    // Rekanan
    $rekanan = $spj->rekanan->nama_rekanan ?? '-';
    $alamat_rekanan = $spj->rekanan->alamat ?? '-';

    $kepada = $spj->rekanan->nama_rekanan ?? '-';
    $namapertok = $spj->rekanan->npwp ?? '-';

    // Nomor BAPP
    $nomor_bapp = $spj->nomor_bapp;

    $unit = \App\Models\UserModel::with('unit')->find(Auth::id())->unit;
    // Ambil nomor SK & tanggal SK
    $sk_nomor = $unit->nomor_sk ?? '-';
    $sk_tanggal = $unit->tanggal_sk
                    ? \Carbon\Carbon::parse($unit->tanggal_sk)->translatedFormat('d F Y')
                    : '-';

    // Tanggal
    $tanggal = \Carbon\Carbon::parse($spj->tanggal)->translatedFormat('d F Y');

    $pdf = PDF::loadView('Spj.Print.BeritaAcaraPengadaanPdf', [
        'spj' => $spj,
        'unit' => $unit,
        'nama_kepala' => $nama_kepala,
        'nip_kepala' => $nip_kepala,
        'pptk' => $pptk,
        'nip_pptk' => $nip_pptk,
        'kepada' => $kepada,
        'rekanan' => $rekanan,
        'alamat_rekanan' => $alamat_rekanan,
        'tanggal' => $tanggal,
        'nomor_bapp' => $nomor_bapp,
        'terbilang' => $terbilang,
        'sk_nomor' => $sk_nomor,
        'sk_tanggal' => $sk_tanggal,
        'namapertok' => $namapertok,
    ])->setPaper([0, 0, 595.28, 935.43], 'portrait');

    return $pdf->stream("berita-acara-pengadaan.pdf");
}

public function cetakBapb($id)
{
    $spj = Spj::with([
        'anggaran.subKegiatan.kegiatan.program',
        'anggaran.pptk',
        'details.rincian.subRincianObjek',
        'rekanan'
    ])->findOrFail($id);

    $unit = \App\Models\UserModel::with('unit')->find(Auth::id())->unit;

    // data pejabat pembuat komitmen
    $ppk = $unit->ppk ?? '-';
    $nip_ppk = $unit->nip_ppk ?? '-';

    // rekanan
    $rekanan = $spj->rekanan->nama_rekanan ?? '.....................................';

    // nomor BAP (bisa dari tabel spj)
    $nomor_bap = $spj->nomor_bapb ?? 'Nomor Tidak Ditemukan'.$spj->tahun;

    // nomor nota pesanan
    $nomor_np = $spj->nomor_nota ?? 'Tahun tidak ditemukan'.$spj->tahun;

    // tanggal format indonesia
    $tanggal = \Carbon\Carbon::parse($spj->tanggal)->translatedFormat('d F Y');

    // total
    $total = $spj->details->sum('jumlah');

    // terbilang
    $terbilang = ucwords(strtolower($this->terbilang($total))) . " Rupiah";

    $pdf = PDF::loadView('Spj.Print.BAPBarang', [
        'spj' => $spj,
        'unit' => $unit,
        'ppk' => $ppk,
        'nip_ppk' => $nip_ppk,
        'rekanan' => $rekanan,
        'nomor_bap' => $nomor_bap,
        'nomor_np' => $nomor_np,
        'tanggal' => $tanggal,
        'total' => $total,
        'terbilang' => $terbilang,
    ])->setPaper([0, 0, 610, 1018], 'portrait');

    return $pdf->stream('BAP-Pemeriksaan-Barang.pdf');
}

public function cetakBAPenyerahan($id)
{
    $spj = Spj::with([
        'anggaran.subKegiatan.kegiatan.program',
        'anggaran.pptk',
        'details',
        'rekanan'
    ])->findOrFail($id);

    $unit = \App\Models\UserModel::with('unit')->find(Auth::id())->unit;

    // Data Kepala / PPK
    $ppk = $unit->ppk ?? '-';
    $nip_ppk = $unit->nip_ppk ?? '-';

    // Rekanan
    $rekanan = $spj->rekanan->nama_rekanan ?? '.....................................';

    // Nomor BAP (ambil nomor BAPB sebagai referensi)
    $nomor_bap = $spj->nomor_bapp ?? 'Nomor Tidak Ditemukan';
    $nomor_bast = $spj->nomor_bast ?? 'Nomor Tidak Ditemukan';

    // Tanggal Indonesia
    $tanggal = \Carbon\Carbon::parse($spj->tanggal)->translatedFormat('d F Y');

    // Total & Terbilang
    $total = $spj->details->sum('jumlah');
    $terbilang = ucwords(strtolower($this->terbilang($total))) . " Rupiah";

    $formatTanggal = $this->formatTanggalHuruf($spj->tanggal);
    $hariHuruf = $formatTanggal['hari'];
    $bulanHuruf = $formatTanggal['bulan'];
    $tahunHuruf = $formatTanggal['tahun'];

    $tanggalObj = \Carbon\Carbon::parse($spj->tanggal);
    $hari = $tanggalObj->translatedFormat('l');

    // render pdf
    $pdf = PDF::loadView('Spj.Print.BAPenyerahanBarang', [
        'spj' => $spj,
        'unit' => $unit,
        'ppk' => $ppk,
        'nip_ppk' => $nip_ppk,
        'rekanan' => $rekanan,
        'nomor_bap' => $nomor_bap,
        'nomor_bast' => $nomor_bast,
        'tanggal' => $tanggal,
        'total' => $total,
        'terbilang' => $terbilang,
        'hariHuruf' => $hariHuruf,
        'bulanHuruf' => $bulanHuruf,
        'tahunHuruf' => $tahunHuruf,
        'hari' => $hari,
    ])->setPaper([0, 0, 595.28, 935.43], 'portrait'); // F4 Potrait

    return $pdf->stream('BA-Penyerahan-Barang.pdf');
}

private function angkaKeHuruf($angka)
{
    $huruf = [
        "", "Satu", "Dua", "Tiga", "Empat", "Lima",
        "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"
    ];

    if ($angka < 12) {
        return $huruf[$angka];
    } elseif ($angka < 20) {
        return $huruf[$angka - 10] . " Belas";
    } elseif ($angka < 100) {
        return $huruf[intval($angka / 10)] . " Puluh " . $huruf[$angka % 10];
    } elseif ($angka < 200) {
        return "Seratus " . $this->angkaKeHuruf($angka - 100);
    } elseif ($angka < 1000) {
        return $huruf[intval($angka / 100)] . " Ratus " . $this->angkaKeHuruf($angka % 100);
    } elseif ($angka < 2000) {
        return "Seribu " . $this->angkaKeHuruf($angka - 1000);
    } elseif ($angka < 1000000) {
        return $this->angkaKeHuruf(intval($angka / 1000)) . " Ribu " . $this->angkaKeHuruf($angka % 1000);
    }
}

private function formatTanggalHuruf($tanggal)
{
    $tgl = \Carbon\Carbon::parse($tanggal);

    $hariHuruf = $this->angkaKeHuruf((int)$tgl->format('d'));
    $bulanHuruf = $tgl->translatedFormat('F'); // Januari, Februari, dst
    $tahunHuruf = $this->angkaKeHuruf((int)$tgl->format('Y'));

    return [
        'hari'  => trim($hariHuruf),
        'bulan' => $bulanHuruf,
        'tahun' => trim($tahunHuruf)
    ];
}

public function cetakPenerimaan($id)
{
    $spj = Spj::with([
        'anggaran.subKegiatan.kegiatan.program',
        'anggaran.unit',
        'details',
        'rekanan'
    ])->findOrFail($id);

    $unit = $spj->anggaran->unit;

    // Format tanggal terpisah
    $tanggalObj = \Carbon\Carbon::parse($spj->tanggal);
    $hari = $tanggalObj->translatedFormat('l');

    $formatTanggal = $this->formatTanggalHuruf($spj->tanggal);
    $hariHuruf = $formatTanggal['hari'];
    $bulanHuruf = $formatTanggal['bulan'];
    $tahunHuruf = $formatTanggal['tahun'];

    // Nomor BA Penerimaan
    $nomor = $spj->nomor_ba_penerimaan;

    // Pejabat & Rekanan
    $pengurus_barang   = $unit->bend_barang ?? '-';
    $nip_pengurus      = $unit->nip_bend_barang ?? '-';

    $ppk               = $unit->ppk ?? '-';
    $nip_ppk           = $unit->nip_ppk ?? '-';

    $pengguna_anggaran = $unit->pengguna_anggaran ?? '-';
    $nip_pengguna      = $unit->nip_pengguna_anggaran ?? '-';

    $rekanan = $spj->rekanan->nama_rekanan ?? '-';

    $unit = \App\Models\UserModel::with('unit')->find(Auth::id())->unit;
    // Ambil nomor SK & tanggal SK
    $sk_nomor = $unit->nomor_sk ?? '-';
    $sk_tanggal = $unit->tanggal_sk
                    ? \Carbon\Carbon::parse($unit->tanggal_sk)->translatedFormat('d F Y')
                    : '-';

    // Total & terbilang
    $total = $spj->details->sum('jumlah');
    $terbilang = ucwords($this->terbilang($total)) . " Rupiah";

    $pdf = PDF::loadView('Spj.Print.PenerimaanBarangPdf', [
        'spj' => $spj,
        'unit' => $unit,
        'hari' => $hari,
        'hariHuruf' => $hariHuruf,
        'bulanHuruf' => $bulanHuruf,
        'tahunHuruf' => $tahunHuruf,
        'nomor' => $nomor,
        'total' => $total,
        'terbilang' => $terbilang,
        'pengurus_barang' => $pengurus_barang,
        'nip_pengurus' => $nip_pengurus,
        'ppk' => $ppk,
        'nip_ppk' => $nip_ppk,
        'pengguna_anggaran' => $pengguna_anggaran,
        'nip_pengguna' => $nip_pengguna,
        'rekanan' => $rekanan,
        'sk_nomor' => $sk_nomor,
        'sk_tanggal' => $sk_tanggal
    ])->setPaper([0, 0, 595.28, 935.43],'portrait');

    return $pdf->stream("BA-Penerimaan-Barang.pdf");
}

public function cetakHonor($id)
{
    $spj = \App\Models\Spj::with([
        'anggaran.subKegiatan.kegiatan.program',
        'daftarHonor',
        'anggaran.pptk'
    ])->findOrFail($id);

    $unit = \App\Models\UserModel::with('unit')->find(Auth::id())->unit;

    // Hitung total pajak, honor, diterima
    $totalHonor  = $spj->daftarHonor->sum('jumlah');
    $totalPajak  = $spj->daftarHonor->sum('nilai_pajak');
    $totalTerima = $spj->daftarHonor->sum('diterima');

    $terbilang = ucwords(strtolower($this->terbilang($totalTerima))) . " Rupiah";
    $tanggal = \Carbon\Carbon::parse($spj->tanggal)->translatedFormat('d F Y');

    $pdf = PDF::loadView('Spj.Print.HonorPdf', [
        'spj'         => $spj,
        'unit'        => $unit,
        'totalHonor'  => $totalHonor,
        'totalPajak'  => $totalPajak,
        'totalTerima' => $totalTerima,
        'terbilang'   => $terbilang,
        'tanggal'     => $tanggal
    ])->setPaper([0, 0, 595.28, 935.43], 'landscape');

    return $pdf->stream("daftar-penerima-honor-pajak.pdf");
}

 public function getRkaForSpj(Request $request)
{
    $user = Auth::user();

    $lock = \App\Models\RkaLock::where('is_active', 1)
        ->where('id_unit', $user->id_unit)
        ->first();

    if (!$lock || $lock->is_locked == 0) {
        return response()->json([]);
    }

    $search = $request->q;

    $rka = Anggaran::with('subKegiatan')

        ->where('id_unit', $user->id_unit)
        ->where('tahun', $user->tahun)

        ->when($search, function ($q) use ($search) {
            $q->whereHas('subKegiatan', function ($sub) use ($search) {
                $sub->where(function ($x) use ($search) {
                    $x->where('nama', 'like', "%$search%")
                      ->orWhere('kode', 'like', "%$search%");
                });
            });
        })

        ->orderBy('id', 'desc')
        ->limit(5) // 🔥 WAJIB UNTUK SELECT2
        ->get()

        ->map(function ($a) {
            return [
                'id'   => $a->id,
                'text' => $a->subKegiatan->kode . ' - ' . $a->subKegiatan->nama,
                'sisapagu' => $a->sisa_pagu,
                'sumber' => $a->sumber_dana, // 🔥 TAMBAH INI
            ];
        });

    return response()->json($rka);
}

 public function getRekanan(Request $request)
{
    $search = $request->q;
    $user   = Auth::user();

    $rekanan = \App\Models\Rekanan::where('id_unit', $user->id_unit)

        ->when($search, function ($q) use ($search) {
            $q->where('nama_rekanan', 'like', "%$search%");
        })

        ->orderBy('nama_rekanan')
        ->limit(5) // 🔥 WAJIB untuk performa
        ->get()

        ->map(function ($r) {
            return [
                'id'   => $r->id,
                'text' => $r->nama_rekanan,
                'alamat' => $r->alamat,
                'npwp' => $r->npwp,
            ];
        });

    return response()->json($rekanan);
}

}
