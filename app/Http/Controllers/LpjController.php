<?php

namespace App\Http\Controllers;

use App\Models\Lpj;
use App\Models\Spj;
use App\Models\Anggaran;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LpjController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* ═══════════════════════════════════════
     |  1. INDEX — Daftar LPJ
    ═══════════════════════════════════════*/
    public function index(Request $request)
    {
        $userId = Auth::id();
        $userx  = UserModel::find($userId);
        $idUnit = $userx->id_unit ?? null;
        $tahun  = $userx->tahun   ?? date('Y');
        $role = strtolower($userx->role ?? 'User');

        $query = Lpj::with(['anggaran.subKegiatan', 'pembuat', 'unit'])
                    ->tahun($tahun);

        // Filter unit sesuai role
        if (!in_array($role, ['Admin', 'Pa', 'Kpa', 'Ppk'])) {
            if ($idUnit) {
                $query->unit($idUnit);
            }
        }

        // Filter dari request
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('bulan')) {
            $query->where('periode_bulan', $request->bulan);
        }

        $lpjList = $query->latest()->paginate(15);

        // Statistik
        $stat = [
            'draft'     => Lpj::tahun($tahun)->when($idUnit, fn($q)=>$q->unit($idUnit))->where('status','draft')->count(),
            'proses'    => Lpj::tahun($tahun)->when($idUnit, fn($q)=>$q->unit($idUnit))->whereIn('status',['diajukan','verifikasi_ppk','disetujui_ppk','validasi_pa'])->count(),
            'approved'  => Lpj::tahun($tahun)->when($idUnit, fn($q)=>$q->unit($idUnit))->whereIn('status',['approved','generated','selesai'])->count(),
            'revisi'    => Lpj::tahun($tahun)->when($idUnit, fn($q)=>$q->unit($idUnit))->whereIn('status',['revisi','ditolak_pa'])->count(),
        ];

        $data = [
            'title'                 => 'Daftar LPJ',
            'active_penerimaan'     => 'active',
            'active_sub'            => 'active',
            'active_side_datalpj'   => 'active',
            'breadcumd'             => 'Penatausahaan',
            'breadcumd1'            => 'LPJ',
            'breadcumd2'            => 'Daftar LPJ',
            'userx'                 => $userx,
            'lpjList'               => $lpjList,
            'stat'                  => $stat,
            'tahun'                 => $tahun,
            'role'                  => $role,
        ];

        return view('LPJ.index', $data);
    }

    /* ═══════════════════════════════════════
     |  2. CREATE — Form Buat LPJ Baru
    ═══════════════════════════════════════*/
    public function create()
    {
        $userx  = UserModel::find(Auth::id());
        $idUnit = $userx->id_unit ?? null;
        $tahun  = $userx->tahun   ?? date('Y');

        // SPJ yang belum masuk LPJ (id_lpj null)
        $spjBelumLpj = Spj::whereNull('id_lpj')
                        ->whereYear('tanggal', $tahun)
                        ->when($idUnit, function($q) use ($idUnit) {
                            $q->where('id_unit', $idUnit);
                        })
                        ->orderBy('tanggal')
                        ->get();

        $anggaranList = Anggaran::with('subKegiatan')
                        ->where('tahun', $tahun)
                        ->when($idUnit, function($q) use ($idUnit) {
                            $q->where('id_unit', $idUnit);
                        })
                        ->get();

        $data = [
            'title'       => 'Buat LPJ Baru',
            'breadcumd'   => 'Penatausahaan',
            'breadcumd1'  => 'LPJ',
            'breadcumd2'  => 'Buat LPJ',
            'userx'       => $userx,
            'spjBelumLpj' => $spjBelumLpj,
            'anggaranList'=> $anggaranList,
            'tahun'       => $tahun,
            'bulanList'   => $this->listBulan(),
        ];

        return view('LPJ.create', $data);
    }

    /* ═══════════════════════════════════════
     |  3. STORE — Simpan LPJ Baru
    ═══════════════════════════════════════*/
    public function store(Request $request)
    {
        $request->validate([
            'jenis'         => 'required|in:GU,LS,TU,UP',
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer',
            'id_anggaran'   => 'required|exists:anggaran,id',
            'saldo_awal'    => 'required|numeric|min:0',
            'spj_ids'       => 'required|array|min:1',
            'spj_ids.*'     => 'exists:spj,id',
        ], [
            'spj_ids.required' => 'Pilih minimal 1 SPJ untuk dimasukkan ke LPJ.',
            'spj_ids.min'      => 'Pilih minimal 1 SPJ untuk dimasukkan ke LPJ.',
        ]);

        $userx  = UserModel::find(Auth::id());
        $idUnit = $userx->id_unit ?? null;

        DB::beginTransaction();
        try {
            // Hitung total dari SPJ yang dipilih
            $totalSpj = Spj::whereIn('id', $request->spj_ids)->sum('total');

            // Buat LPJ
            $lpj = Lpj::create([
                'nomor_lpj'      => Lpj::generateNomor(
                                        $request->jenis,
                                        $request->periode_bulan,
                                        $request->periode_tahun,
                                        $idUnit
                                    ),
                'id_anggaran'    => $request->id_anggaran,
                'id_unit'        => $idUnit,
                'created_by'     => Auth::id(),
                'jenis'          => $request->jenis,
                'periode_bulan'  => $request->periode_bulan,
                'periode_tahun'  => $request->periode_tahun,
                'saldo_awal'     => $request->saldo_awal,
                'total_spj'      => $totalSpj,
                'saldo_akhir'    => $request->saldo_awal - $totalSpj,
                'keterangan'     => $request->keterangan,
                'status'         => 'draft',
            ]);

            // Hubungkan SPJ ke LPJ ini
            Spj::whereIn('id', $request->spj_ids)->update(['id_lpj' => $lpj->id]);

            DB::commit();
            return redirect()->route('lpj.show', $lpj->id)
                             ->with('success', 'LPJ berhasil dibuat. Silakan ajukan ke PPK.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat LPJ: ' . $e->getMessage());
        }
    }

    /* ═══════════════════════════════════════
     |  4. SHOW — Detail LPJ
    ═══════════════════════════════════════*/
    public function show($id)
    {
        $lpj = Lpj::with([
                    'spjList.anggaran.subKegiatan',
                    'anggaran.subKegiatan',
                    'unit',
                    'pembuat',
                    'ppk',
                    'pa',
                ])->findOrFail($id);

        $userx = UserModel::find(Auth::id());

        $data = [
            'title'      => 'Detail LPJ - ' . $lpj->nomor_lpj,
            'breadcumd'  => 'Penatausahaan',
            'breadcumd1' => 'LPJ',
            'breadcumd2' => 'Detail LPJ',
            'userx'      => $userx,
            'lpj'        => $lpj,
            'role'       => $userx->role ?? 'User',
        ];

        return view('LPJ.show', $data);
    }

    /* ═══════════════════════════════════════
     |  5. AJUKAN ke PPK (Bendahara)
    ═══════════════════════════════════════*/
    public function ajukan($id)
    {
        $lpj = Lpj::findOrFail($id);

        if (!in_array($lpj->status, ['draft','revisi','ditolak_pa'])) {
            return back()->with('error','LPJ tidak dapat diajukan pada status ini');
        }

        if ($lpj->spjList()->count() === 0) {
            return back()->with('error', 'LPJ harus memiliki minimal 1 SPJ.');
        }

        $lpj->hitungUlangTotal();
        $lpj->update(['status' => 'diajukan']);

        return back()->with('success', 'LPJ berhasil diajukan ke PPK untuk diverifikasi.');
    }

    /* ═══════════════════════════════════════
     |  6. VERIFIKASI PPK — Form
    ═══════════════════════════════════════*/
    public function formVerifikasiPpk($id)
    {
        $lpj   = Lpj::with(['spjList', 'anggaran', 'pembuat'])->findOrFail($id);
        $userx = UserModel::find(Auth::id());

        if (!in_array($lpj->status, ['diajukan', 'verifikasi_ppk'])) {
            return redirect()->route('lpj.show', $id)
                             ->with('error', 'LPJ tidak dalam status verifikasi PPK.');
        }

        $lpj->update(['status' => 'verifikasi_ppk', 'id_ppk' => Auth::id()]);

        $data = [
            'title'      => 'Verifikasi LPJ - PPK',
            'breadcumd'  => 'Verifikasi',
            'breadcumd1' => 'LPJ',
            'breadcumd2' => 'Verifikasi PPK',
            'userx'      => $userx,
            'lpj'        => $lpj,
        ];
        return view('LPJ.verifikasi_ppk', $data);
    }

    /* ═══════════════════════════════════════
     |  7. PROSES VERIFIKASI PPK
    ═══════════════════════════════════════*/
    public function prosesVerifikasiPpk(Request $request, $id)
    {
        $request->validate([
            'keputusan'    => 'required|in:setuju,revisi',
            'catatan_ppk'  => 'nullable|string|max:1000',
        ]);

        $lpj = Lpj::findOrFail($id);

        if ($request->keputusan === 'setuju') {
            $lpj->update([
                'status'          => 'disetujui_ppk',
                'catatan_ppk'     => $request->catatan_ppk,
                'tgl_verifikasi'  => now(),
                'id_ppk'          => Auth::id(),
            ]);
            return redirect()->route('lpj.show', $id)
                             ->with('success', 'LPJ disetujui PPK. Diteruskan ke PA/KPA.');
        } else {
            $lpj->update([
                'status'          => 'revisi',
                'catatan_ppk'     => $request->catatan_ppk,
                'tgl_verifikasi'  => now(),
                'id_ppk'          => Auth::id(),
            ]);
            return redirect()->route('lpj.show', $id)
                             ->with('warning', 'LPJ dikembalikan ke Bendahara untuk direvisi.');
        }
    }

    /* ═══════════════════════════════════════
     |  8. VALIDASI / APPROVAL PA/KPA — Form
    ═══════════════════════════════════════*/
    public function formApprovalPa($id)
    {
        $lpj   = Lpj::with(['spjList', 'anggaran', 'pembuat', 'ppk'])->findOrFail($id);
        $userx = UserModel::find(Auth::id());

        if ($lpj->status !== 'disetujui_ppk') {
            return redirect()->route('lpj.show', $id)
                             ->with('error', 'LPJ belum diverifikasi PPK.');
        }

        $lpj->update(['status' => 'validasi_pa', 'id_pa' => Auth::id()]);

        $data = [
            'title'      => 'Validasi LPJ - PA/KPA',
            'breadcumd'  => 'Validasi',
            'breadcumd1' => 'LPJ',
            'breadcumd2' => 'Approval PA/KPA',
            'userx'      => $userx,
            'lpj'        => $lpj,
        ];
        return view('LPJ.approval_pa', $data);
    }

    /* ═══════════════════════════════════════
     |  9. PROSES APPROVAL PA/KPA
    ═══════════════════════════════════════*/
    public function prosesApprovalPa(Request $request, $id)
    {
        $request->validate([
            'keputusan'  => 'required|in:setuju,tolak',
            'catatan_pa' => 'nullable|string|max:1000',
        ]);

        $lpj = Lpj::findOrFail($id);

        if ($request->keputusan === 'setuju') {
            $lpj->update([
                'status'       => 'approved',
                'catatan_pa'   => $request->catatan_pa,
                'tgl_approval' => now(),
                'id_pa'        => Auth::id(),
            ]);
            return redirect()->route('lpj.show', $id)
                             ->with('success', 'LPJ disetujui PA/KPA. Siap untuk di-generate.');
        } else {
            $lpj->update([
                'status'       => 'ditolak_pa',
                'catatan_pa'   => $request->catatan_pa,
                'tgl_approval' => now(),
                'id_pa'        => Auth::id(),
            ]);
            return redirect()->route('lpj.show', $id)
                             ->with('danger', 'LPJ ditolak PA/KPA.');
        }
    }

    /* ═══════════════════════════════════════
     |  10. TAMBAH SPJ ke LPJ (saat draft)
    ═══════════════════════════════════════*/
    public function tambahSpj(Request $request, $id)
    {
        $request->validate(['spj_ids' => 'required|array|min:1']);

        $lpj = Lpj::findOrFail($id);

        if (!in_array($lpj->status, ['draft', 'revisi'])) {
            return back()->with('error', 'SPJ hanya bisa ditambah saat status Draft atau Revisi.');
        }

        Spj::whereIn('id', $request->spj_ids)
           ->whereNull('id_lpj')
           ->update(['id_lpj' => $lpj->id]);

        $lpj->hitungUlangTotal();

        return back()->with('success', 'SPJ berhasil ditambahkan ke LPJ.');
    }

    /* ═══════════════════════════════════════
     |  11. HAPUS SPJ dari LPJ (saat draft)
    ═══════════════════════════════════════*/
    public function hapusSpj($idLpj, $idSpj)
    {
        $lpj = Lpj::findOrFail($idLpj);

        if (!in_array($lpj->status, ['draft', 'revisi'])) {
            return back()->with('error', 'SPJ tidak bisa dihapus dari LPJ ini.');
        }

        Spj::where('id', $idSpj)->where('id_lpj', $idLpj)
           ->update(['id_lpj' => null]);

        $lpj->hitungUlangTotal();

        return back()->with('success', 'SPJ berhasil dilepas dari LPJ.');
    }

    /* ═══════════════════════════════════════
     |  12. GENERATE PDF LPJ
    ═══════════════════════════════════════*/
    public function generate($id)
    {
        $lpj = Lpj::with([
                    'spjList.anggaran.subKegiatan',
                    'anggaran.subKegiatan',
                    'unit',
                    'pembuat',
                    'ppk',
                    'pa',
                ])->findOrFail($id);

        if ($lpj->status !== 'approved') {
            return back()->with('error', 'LPJ harus disetujui PA/KPA sebelum di-generate.');
        }

        // Ambil data OPD dari user
        $userx = UserModel::find($lpj->created_by);

        $data = [
            'lpj'   => $lpj,
            'userx' => $userx,
            'tahun' => $lpj->periode_tahun,
        ];

        $pdf = Pdf::loadView('LPJ.pdf_lpj', $data)
                  ->setPaper('a4', 'portrait');

        // Simpan file
        $filename = 'LPJ_' . str_replace('/', '_', $lpj->nomor_lpj) . '.pdf';
        $path     = 'lpj/' . $filename;
        $pdf->save(storage_path('app/public/' . $path));

        // Update status
        $lpj->update([
            'status'       => 'generated',
            'tgl_generate' => now(),
            'file_lpj'     => $path,
        ]);

        return redirect()->route('lpj.show', $id)
                         ->with('success', 'LPJ berhasil di-generate. Silakan cetak.');
    }

    /* ═══════════════════════════════════════
     |  13. CETAK / DOWNLOAD PDF
    ═══════════════════════════════════════*/
    public function cetak($id)
    {
        $lpj = Lpj::with([
                    'spjList.anggaran.subKegiatan',
                    'anggaran.subKegiatan',
                    'unit', 'pembuat', 'ppk', 'pa',
                ])->findOrFail($id);

        if (!in_array($lpj->status, ['generated', 'selesai'])) {
            // Generate langsung jika approved
            if ($lpj->status === 'approved') {
                return $this->generate($id);
            }
            return back()->with('error', 'LPJ belum di-generate.');
        }

        $lpj->increment('jumlah_cetak');
        if ($lpj->status === 'generated') {
            $lpj->update(['status' => 'selesai']);
        }

        $userx = UserModel::find($lpj->created_by);
        $pdf   = Pdf::loadView('LPJ.pdf_lpj', [
                    'lpj'   => $lpj,
                    'userx' => $userx,
                    'tahun' => $lpj->periode_tahun,
                 ])->setPaper('a4', 'portrait');

        $filename = 'LPJ_' . str_replace('/', '_', $lpj->nomor_lpj) . '.pdf';
        return $pdf->download($filename);
    }

    /* ═══════════════════════════════════════
     |  14. PREVIEW PDF (inline)
    ═══════════════════════════════════════*/
    public function preview($id)
    {
        $lpj   = Lpj::with(['spjList.anggaran.subKegiatan','anggaran.subKegiatan','unit','pembuat','ppk','pa'])->findOrFail($id);
        $userx = UserModel::find($lpj->created_by);

        $pdf = Pdf::loadView('LPJ.pdf_lpj', [
                    'lpj'   => $lpj,
                    'userx' => $userx,
                    'tahun' => $lpj->periode_tahun,
                 ])->setPaper('a4', 'portrait');

        return $pdf->stream('preview_lpj.pdf');
    }

    /* ═══════════════════════════════════════
     |  HELPER
    ═══════════════════════════════════════*/
    private function listBulan()
    {
        return [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
            5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
            9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
        ];
    }

    public function unapprovalPa($id)
    {
        DB::beginTransaction();

        try {

            $lpj = Lpj::findOrFail($id);

            // ✅ PENGAMAN
            if ($lpj->status != 'approved') {
                return back()->with('error', 'Status LPJ tidak valid untuk unapproval');
            }

            $lpj->update([
                'status' => 'disetujui_ppk',
                'id_pa' => null,
                'tgl_approval' => null,
            ]);

            DB::commit();

            return back()->with('success', 'Approval PA dibatalkan');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function unverifikasiPpk($id)
    {
        DB::beginTransaction();

        try {

            $lpj = Lpj::findOrFail($id);

            if ($lpj->status != 'disetujui_ppk') {
                return back()->with('error', 'Status LPJ tidak valid untuk unverifikasi');
            }

            $lpj->update([
                'status' => 'diajukan',
                'id_ppk' => null,
                'tgl_verifikasi' => null,
                'catatan_ppk' => null,
            ]);

            DB::commit();

            return back()->with('success', 'Verifikasi PPK dibatalkan');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $lpj = Lpj::findOrFail($id);

            /* ✅ PENGAMAN STATUS */
            if (!in_array($lpj->status, ['draft','diajukan','revisi'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'LPJ harus dibatalkan approval & verifikasi terlebih dahulu'
                ], 403);
            }

            /* ✅ LEPASKAN SPJ */
            Spj::where('id_lpj', $lpj->id)->update([
                'id_lpj' => null
            ]);

            /* ✅ HAPUS FISIK 🔥 */
            $lpj->forceDelete();   // 🔥 INI KUNCINYA

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'LPJ berhasil dihapus permanen'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

}