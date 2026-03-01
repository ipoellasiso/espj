<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\RincianAnggaran;
use App\Imports\AnggaranImport;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\RkaExport;
use App\Models\Akun;
use App\Models\Kelompok;
use App\Models\Jenis;
use App\Models\Objek;
use App\Models\RincianObjek;
use App\Models\SubRincianObjek;
use App\Exports\TemplateRincianExport;
use App\Models\RkaLock;

class LaporanAnggaranController extends Controller
{
    // === Halaman utama daftar RKA ===
    public function index(Request $request)
    {
        // $user = Auth::guard('web')->user()->id;
        $user = Auth::guard('web')->user(); // ambil user yang login

        // $rka_locks = RkaLock::orderBy('id')->get();
        // $active_lock = RkaLock::where('is_active', 1)
        //     ->where('id_unit', $user->id_unit)
        //     ->where('tahun', $user->tahun)
        //     ->first();

        // ✅ Filter hanya milik user yang login (id_unit & tahun)
        $rka_locks = RkaLock::where('id_unit', $user->id_unit)
                        ->where('tahun', $user->tahun)
                        ->orderBy('id')
                        ->get();

        $active_lock = RkaLock::where('is_active', 1)
            ->where('id_unit', $user->id_unit)
            ->where('tahun', $user->tahun)
            ->first();
        
        $data = [
            'title'                 => 'Data RKA',
            'active_laporan'        => 'active',
            'breadcumd'             => 'Laporan',
            'breadcumd1'            => 'Anggaran',
            'breadcumd2'            => 'RKA',
            'userx'                 => Auth::user(),
            'pptk' => \App\Models\Pptk::where('id_unit', Auth::user()->id_unit)->get(),
            
            'rka_locks'      => $rka_locks,
            'active_lock'    => $active_lock,
        ];

        if ($request->ajax()) {
            $anggaran = Anggaran::with('subKegiatan.kegiatan.program.bidang.urusan', 'pptk')
                ->where('id_unit', $user->id_unit)
                ->where('tahun', $user->tahun);

            return DataTables::of($anggaran)
                ->addIndexColumn()
                ->addColumn('program', function ($row) {
                    return ($row->subKegiatan->kegiatan->program->kode ?? '-') . ' ' . ($row->subKegiatan->kegiatan->program->nama ?? '-');
                })
                ->addColumn('kegiatan', function ($row) {
                    return ($row->subKegiatan->kegiatan->kode ?? '-') . ' ' . ($row->subKegiatan->kegiatan->nama ?? '-');
                })
                ->addColumn('sub_kegiatan', function ($row) {
                    return ($row->subKegiatan->kode ?? '-') . ' ' . ($row->subKegiatan->nama ?? '-');
                })
                ->addColumn('pagu_anggaran', function ($row) {
                    return number_format($row->pagu_anggaran);
                })
                ->addColumn('realisasi', function ($row) {
                    return $row->realisasi ?? 0;
                })
                ->addColumn('sisa_pagu', function ($row) {
                    return ($row->pagu_anggaran ?? 0) - ($row->realisasi ?? 0);
                })
                ->addColumn('pptk', function ($row) {
                    return $row->pptk->nama ?? '-';
                })
                ->addColumn('aksi', function ($row) {
                    $isLocked = $this->isRkaLocked();

                    $btn = '<a href="' . route('rka.show', $row->id) . '" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a> ';

                    if ($isLocked || $row->realisasi > 0) {
                        return $btn . '<span class="badge bg-secondary">
                            <i class="bi bi-lock-fill"></i> Terkunci
                        </span>';
                    }

                    return $btn . '
                        <a href="javascript:void(0)" data-id="'.$row->id.'" class="editRka btn btn-sm btn-primary">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="javascript:void(0)" data-id="'.$row->id.'" class="deleteRka btn btn-sm btn-danger">
                            <i class="bi bi-trash3"></i>
                        </a>';
                })
                // ✅ Filter untuk kolom computed (tidak ada di DB langsung)
                ->filterColumn('program', function ($query, $keyword) {
                    $query->whereHas('subKegiatan.kegiatan.program', function ($q) use ($keyword) {
                        $q->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('kode', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('kegiatan', function ($query, $keyword) {
                    $query->whereHas('subKegiatan.kegiatan', function ($q) use ($keyword) {
                        $q->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('kode', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('sub_kegiatan', function ($query, $keyword) {
                    $query->whereHas('subKegiatan', function ($q) use ($keyword) {
                        $q->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('kode', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('pagu_anggaran', function ($query, $keyword) {
                    $query->where('pagu_anggaran', 'like', "%{$keyword}%");
                })
                ->filterColumn('pptk', function ($query, $keyword) {
                    $query->whereHas('pptk', function ($q) use ($keyword) {
                        $q->where('nama', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('laporan.index', $data);
    }

    // === Tampilkan detail RKA ===
    public function show($id)
    {
        $user = Auth::guard('web')->user();

        // Cegah akses ke RKA unit lain
        $anggaran = Anggaran::with([
            'subKegiatan.kegiatan.program.bidang.urusan',
            'unit' // ✅ tambahkan ini
            ])->findOrFail($id);
            
        // Ambil semua rincian untuk ditampilkan di tabel bawah
        $subRincian = $anggaran->rincian;
        $rincian = $this->buildHierarki($subRincian);

        // $active_lock = \App\Models\RkaLock::where('is_active', 1)->first();
        $active_lock = RkaLock::where('is_active', 1)
            ->where('id_unit', $user->id_unit)
            ->where('tahun', $user->tahun)
            ->first();
        
        $data = [
            'title'         => 'Detail RKA',
            'active_home'   => '',
            'active_laporan'=> 'active',
            'breadcumd'     => 'Laporan',
            'breadcumd1'    => 'Anggaran',
            'breadcumd2'    => 'Detail RKA',
            'userx'         => Auth::user(),
            'anggaran'      => $anggaran,
            'rincian'       => $rincian,
            'active_lock'   => $active_lock,   // <–– KIRIM KE BLADE
        ];

        return view('laporan.show', $data);
    }

    private function buildHierarki($subRincian)
    {
        $tree = [];

        foreach ($subRincian as $item) {
            $kode = $item->kode_rekening;
            $parts = explode('.', $kode);
            $path = '';

            foreach ($parts as $index => $part) {
                $path = $path ? "$path.$part" : $part;

                if (!isset($tree[$path])) {
                    $tree[$path] = [
                        'kode'      => $path,
                        'uraian'    => $this->getUraianFromKode($path),
                        'jumlah'    => 0,
                        'koefisien' => null,
                        'satuan'    => null,
                        'harga'     => null,
                        'id'        => null,
                        'children'  => []
                    ];
                }

                if ($index === count($parts) - 1) {
                    $childNode = [
                        'id'        => $item->id,
                        'kode'      => $item->kode_rekening,
                        'uraian'    => $item->uraian ?? $this->getUraianFromKode($path),
                        'koefisien' => $item->koefisien,
                        'satuan'    => $item->satuan,
                        'harga'     => $item->harga,
                        'jumlah'    => $item->jumlah,
                        'children'  => []
                    ];

                    $tree[$path]['children'][] = $childNode;
                    $tree[$path]['jumlah'] += $item->jumlah ?? 0;
                }
            }
        }

        // Hitung total parent
        $tree = $this->sumParentLevels($tree);

        // Ambil hanya node root
        $root = [];
        foreach ($tree as $kode => $node) {
            if ($this->getParentKode($kode) === null) {
                $root[] = $node;
            }
        }

        // ✅ Urutkan berdasarkan kode numerik (natural order)
        usort($root, fn($a, $b) => $this->sortKode($a['kode'], $b['kode']));

        // 🔁 Urutkan juga children secara rekursif
        foreach ($root as &$node) {
            $node['children'] = $this->sortChildren($node['children']);
        }

        return $root;
    }

    private function sumParentLevels($tree)
    {
        // Urutkan berdasarkan panjang kode supaya anak dihitung dulu
        uksort($tree, fn($a, $b) => strlen($b) <=> strlen($a));

        foreach ($tree as $kode => &$node) {
            $parent = $this->getParentKode($kode);
            if ($parent && isset($tree[$parent])) {
                $tree[$parent]['jumlah'] += $node['jumlah'];
                $tree[$parent]['children'][] = &$node;
            }
        }

        // Balik urutan supaya hierarki naik ke atas
        uksort($tree, fn($a, $b) => strlen($a) <=> strlen($b));

        return $tree;
    }

    /**
     * Ambil uraian dari kode (bisa pakai tabel referensi / static array)
     */
    private function getUraianFromKode($kode)
    {
        $models = [
            SubRincianObjek::class,
            RincianObjek::class,
            Objek::class,
            Jenis::class,
            Kelompok::class,
            Akun::class,
        ];

        foreach ($models as $model) {
            $data = $model::where('kode', $kode)->first();
            if ($data) return e($data->uraian); // aman dari XSS
        }

        // jika tidak ditemukan, tampilkan teks merah dengan ikon
        return '<span class="rek-error" title="Kode rekening ini tidak ditemukan!" style="color:#dc3545; font-weight:bold;">
                    <i class="bi bi-exclamation-triangle-fill"></i> 
                    Upss Kode Rek. ini Tidak Ditemukan!, Mohon Periksa Kembali Kode Rekening Tersebut.
                </span>';
    }

    /**
     * Ambil parent dari kode
     */
    private function getParentKode($kode)
    {
        $parts = explode('.', $kode);
        array_pop($parts);
        return count($parts) ? implode('.', $parts) : null;
    }

    // === CRUD Tambah/Edit/Hapus via AJAX ===

    public function store(Request $request)
    {
        $request->validate([
            'id_subkegiatan' => 'nullable|numeric',
            'sumber_dana'    => 'required',
            'pagu_anggaran'  => 'required|numeric',
        ]);

        // ✅ Ambil ID unit dari user login
        $idUnit = Auth::user()->id_unit ?? null;

        // 🔐 CEK TAHAP RKA TERKUNCI
        if ($this->isRkaLocked()) {
            return response()->json([
                'error' => 'RKA dikunci dan tidak bisa disimpan.'
            ], 403);
        }

        // 💾 SIMPAN RKA
        $anggaran = Anggaran::updateOrCreate(
            ['id' => $request->id],
            [
                'id_subkegiatan' => $request->id_subkegiatan,
                'sumber_dana'    => $request->sumber_dana,
                'pagu_anggaran'  => $request->pagu_anggaran,
                'tahun'          => Auth::user()->tahun ?? date('Y'),
                'id_unit'        => $idUnit,
                'id_pptk'        => $request->id_pptk,
            ]
        );

        return response()->json([
            'success' => true,
            'data'    => $anggaran
        ]);
    }

    // public function edit($id)
    // {
    //     $anggaran = Anggaran::find($id);
    //     return response()->json($anggaran);
    // }

    public function edit($id)
    {
        $anggaran = Anggaran::findOrFail($id);

        if ($this->isRkaLocked()) {
            return response()->json([
                'lock' => true,
                'message' => 'RKA dikunci dan tidak dapat diedit.'
            ]);
        }

        $anggaran = DB::table('anggaran')
            ->leftJoin('sub_kegiatan', 'sub_kegiatan.id', '=', 'anggaran.id_subkegiatan')
            ->select('anggaran.*', DB::raw("CONCAT(sub_kegiatan.kode, ' - ', sub_kegiatan.nama) as sub_kegiatan_text"), 'anggaran.id_pptk')
            ->where('anggaran.id', $id)
            ->first();

        return response()->json($anggaran);
    }

    public function destroy($id)
    {
        if ($this->isRkaLocked()) {
            return response()->json([
                'error' => 'RKA dikunci dan tidak dapat dihapus.'
            ], 403);
        }

        Anggaran::find($id)->delete();
        return response()->json(['success' => 'Data berhasil dihapus']);
    }

    // ========================= CRUD AJAX =========================
    public function storeRincian(Request $request, $id_anggaran)
    {
        if ($this->isRkaLocked()) {
            return response()->json([
                'lock' => true,
                'message' => 'Rincian tidak bisa diubah karena RKA dikunci.'
            ]);
        }

        $validated = $request->validate([
            'kode_rekening' => 'required|string|max:50',
            'uraian'        => 'required|string|max:255',
            'koefisien'     => 'nullable|numeric',
            'satuan'        => 'nullable|string|max:100',
            'harga'         => 'nullable|numeric',
            'jumlah'        => 'nullable|numeric',
        ]);

        $validated['id_anggaran'] = $id_anggaran;

        // Hitung otomatis jumlah jika belum diisi
        if (empty($validated['jumlah'])) {
            $validated['jumlah'] = ($validated['harga'] ?? 0) * ($validated['koefisien'] ?? 0);
        }

        \App\Models\RincianAnggaran::updateOrCreate(
            ['id' => $request->id],
            $validated
        );

        // Hitung ulang total pagu dari semua rincian
        $this->updateTotalAnggaran($id_anggaran);

        return response()->json(['success' => true]);
    }

    public function editRincian($id)
    {
        $rincian = RincianAnggaran::with('anggaran')->findOrFail($id);

        if ($this->isRkaLocked()) {
            return response()->json([
                'lock' => true,
                'message' => 'Rincian tidak bisa diubah karena RKA dikunci.'
            ]);
        }

        return response()->json($rincian);
    }

    public function deleteRincian($id)
    {
        return DB::transaction(function () use ($id) {

            // ambil rincian, pastikan ada
            $rincian = RincianAnggaran::findOrFail($id);

            // simpan id anggaran untuk dihitung ulang
            $idAnggaran = $rincian->id_anggaran;

            // hapus rincian
            $rincian->delete();

            // hitung ulang total pagu berdasarkan sisa sub-rincian
            $total = RincianAnggaran::where('id_anggaran', $idAnggaran)->sum('jumlah');

            if ($this->isRkaLocked()) {
                return response()->json([
                    'lock' => true,
                    'message' => 'Rincian tidak bisa diubah karena RKA dikunci.'
                ]);
            }

            // update anggaran
            $anggaran = Anggaran::find($idAnggaran);
            if ($anggaran) {
                $anggaran->pagu_anggaran = $total;
                $anggaran->save();
            }

            // kembalikan data berguna ke frontend
            return response()->json([
                'success' => true,
                'pagu_baru' => $total,
                'message' => 'Rincian berhasil dihapus dan pagu diperbarui.'
            ]);
        }, 5);
    }

    public function exportPdf($id)
    {
        $anggaran = Anggaran::with(['rincian', 'subKegiatan.kegiatan.program.bidang.urusan'])
        ->findOrFail($id);
        $rincian = $this->buildHierarki($anggaran->rincian);

        $pdf = PDF::loadView('laporan.Export.Pdf', compact('anggaran', 'rincian'))
            ->setPaper('a4', 'portrait');

        // Bersihkan karakter ilegal dari nama subkegiatan
        $namaFile = preg_replace('/[\/\\\\:*?"<>|]/', '_', $anggaran->subKegiatan->nama);

        return $pdf->download('RKA-'.$namaFile.'.pdf');
    }

    public function exportExcel($id)
    {
        return Excel::download(new RkaExport($id), 'RKA.xlsx');
    }

    private function updateTotalAnggaran($id_anggaran)
    {
        $total = RincianAnggaran::where('id_anggaran', $id_anggaran)->sum('jumlah');

        $anggaran = Anggaran::find($id_anggaran);
        if ($anggaran) {
            $anggaran->pagu_anggaran = $total;
            $anggaran->save();
        }
    }

    public function importRincian(Request $request, $id_anggaran)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new \App\Imports\RincianImport($id_anggaran), $request->file('file'));
            $this->updateTotalAnggaran($id_anggaran);

            return response()->json(['success' => true, 'message' => 'Data rincian berhasil diimpor.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimpor data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new TemplateRincianExport, 'template_rincian.xlsx');
    }

    public function getTotalPagu($id)
    {
        $total = \App\Models\RincianAnggaran::where('id_anggaran', $id)->sum('jumlah');
        return response()->json(['total' => $total]);
    }

    public function getSubKegiatan(Request $request)
    {
        $user = Auth::user();
        $search = $request->q;

        $data = DB::table('sub_kegiatan as sk')
            ->join('sub_kegiatan_unit as sku', 'sku.id_sub_kegiatan', '=', 'sk.id')
            ->where('sku.id_unit', $user->id_unit)
            ->when($search, function ($q) use ($search) {
                $q->where('sk.nama', 'like', "%$search%")
                ->orWhere('sk.kode', 'like', "%$search%");
            })
            ->orderBy('sk.kode')
            ->limit(20)
            ->get([
                'sk.id',
                DB::raw("CONCAT(sk.kode,' - ',sk.nama) as text")
            ]);

        return response()->json($data);
    }

    private function sortKode($a, $b)
    {
        // Hilangkan titik dan ubah ke integer untuk urutan alami
        return intval(str_replace('.', '', $a)) <=> intval(str_replace('.', '', $b));
    }

    private function sortChildren($children)
    {
        if (empty($children)) return $children;

        usort($children, fn($a, $b) => $this->sortKode($a['kode'], $b['kode']));

        foreach ($children as &$child) {
            if (!empty($child['children'])) {
                $child['children'] = $this->sortChildren($child['children']);
            }
        }

        return $children;
    }

    private function isRkaLocked()
    {
        $user = Auth::user();

        $active = RkaLock::where('is_active', 1)
            ->where('id_unit', $user->id_unit)
            ->where('tahun', $user->tahun) // 🔥 KUNCI UTAMA
            ->first();

        if (!$active) return false;

        return $active->is_locked == 1;
    }

    public function setActive(Request $request)
    {
        RkaLock::where('id_unit', Auth::user()->id_unit)
            ->where('tahun', Auth::user()->tahun)
            ->update(['is_active' => 0]);

        RkaLock::where('tahap', $request->tahap)
            ->where('id_unit', Auth::user()->id_unit)
            ->where('tahun', Auth::user()->tahun)
            ->update(['is_active' => 1]);

        return response()->json(['success' => true]);
    }

    public function toggleLock(Request $request)
    {
        $user = Auth::user();

        $lock = RkaLock::firstOrCreate(
            [
                'tahap'   => $request->tahap,
                'id_unit' => $user->id_unit,
                'tahun'   => $user->tahun, // 🔥
            ],
            [
                'is_active' => 1,
                'is_locked' => 0
            ]
        );

        $lock->is_locked = !$lock->is_locked;
        $lock->save();

        return response()->json([
            'success' => true,
            'message' => $lock->is_locked
                ? 'Tahap OPD berhasil dikunci'
                : 'Tahap OPD berhasil dibuka'
        ]);
    }

    public function getUrusan(Request $request)
    {
        return \App\Models\Urusan::

            when($request->q, function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%')
                ->orWhere('kode', 'like', '%' . $request->q . '%');
            })

            ->select(
                'id',
                DB::raw("CONCAT(kode,' - ',nama) as text")
            )
            ->orderBy('kode')
            ->limit(20)
            ->get();
    }

    public function getBidang(Request $request)
    {
        return \App\Models\BidangUrusan::where('id_urusan', $request->id_urusan)

            ->when($request->q, function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%')
                ->orWhere('kode', 'like', '%' . $request->q . '%');
            })

            ->select('id', DB::raw("CONCAT(kode,' - ',nama) as text"))
            ->orderBy('kode')
            ->limit(20)
            ->get();
    }

    public function getProgram(Request $request)
    {
        return \App\Models\Program::where('id_bidang', $request->id_bidang)

            ->when($request->q, function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%')
                ->orWhere('kode', 'like', '%' . $request->q . '%');
            })

            ->select('id', DB::raw("CONCAT(kode,' - ',nama) as text"))
            ->orderBy('kode')
            ->limit(20)
            ->get();
    }

    public function getKegiatan(Request $request)
    {
        return \App\Models\Kegiatan::where('id_program', $request->id_program)

            ->when($request->q, function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%')
                ->orWhere('kode', 'like', '%' . $request->q . '%');
            })

            ->select('id', DB::raw("CONCAT(kode,' - ',nama) as text"))
            ->orderBy('kode')
            ->limit(20)
            ->get();
    }

    public function getSubKegiatanByKegiatan(Request $request)
    {
        return \App\Models\SubKegiatan::where('id_kegiatan', $request->id_kegiatan)

            ->when($request->q, function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->q . '%')
                ->orWhere('kode', 'like', '%' . $request->q . '%');
            })

            ->select('id', DB::raw("CONCAT(kode,' - ',nama) as text"))
            ->orderBy('kode')
            ->limit(20)
            ->get();
    }

}
