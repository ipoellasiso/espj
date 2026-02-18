<?php

namespace App\Http\Controllers;

use App\Models\Spj;
use App\Models\Anggaran;
use App\Models\UserModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $userId  = Auth::guard('web')->user()->id;
        $userx   = UserModel::where('id', $userId)
                    ->first(['fullname', 'role', 'gambar', 'tahun', 'id_unit']);

        $tahun  = $userx->tahun   ?? date('Y');
        $idUnit = $userx->id_unit ?? null;

        /* ── VARIABEL LAYOUT (dibutuhkan Template/Layout.blade.php) ── */
        $title      = 'Dashboard';
        $breadcumd  = 'Home';
        $breadcumd1 = 'Dashboard';
        $breadcumd2 = 'Dashboard';
        $active_home = 'active';

        /* ── STAT CARDS ── */
        $qLS = Spj::whereYear('tanggal', $tahun)->where('sumber_dana', 'LS');
        if ($idUnit) { $qLS->where('id_unit', $idUnit); }
        $total_ls = $qLS->sum('total');

        $qGU = Spj::whereYear('tanggal', $tahun)->where('sumber_dana', 'GU');
        if ($idUnit) { $qGU->where('id_unit', $idUnit); }
        $total_gu = $qGU->sum('total');

        $qAll = Spj::whereYear('tanggal', $tahun);
        if ($idUnit) { $qAll->where('id_unit', $idUnit); }
        $total_spj_nilai = $qAll->sum('total');

        $qCnt = Spj::whereYear('tanggal', $tahun);
        if ($idUnit) { $qCnt->where('id_unit', $idUnit); }
        $total_spj_count = $qCnt->count();

        /* ── ANGGARAN ── */
        $qPagu = Anggaran::where('tahun', $tahun);
        if ($idUnit) { $qPagu->where('id_unit', $idUnit); }
        $pagu_total = $qPagu->sum('pagu_anggaran');

        $qReal = Anggaran::where('tahun', $tahun);
        if ($idUnit) { $qReal->where('id_unit', $idUnit); }
        $realisasi_total = $qReal->sum('realisasi');

        $qSisa = Anggaran::where('tahun', $tahun);
        if ($idUnit) { $qSisa->where('id_unit', $idUnit); }
        $sisa_pagu_total = $qSisa->sum('sisa_pagu');

        $persen_realisasi = $pagu_total > 0
                            ? round(($realisasi_total / $pagu_total) * 100, 1) : 0;
        $realisasi_ls = $pagu_total > 0
                        ? min(round(($total_ls / $pagu_total) * 100, 1), 100) : 0;
        $realisasi_gu = $pagu_total > 0
                        ? min(round(($total_gu / $pagu_total) * 100, 1), 100) : 0;

        /* ── TREN BULANAN ── */
        $bulan_labels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        $qTLS = Spj::selectRaw('MONTH(tanggal) as bulan, SUM(total) as nilai')
                    ->whereYear('tanggal', $tahun)->where('sumber_dana', 'LS');
        if ($idUnit) { $qTLS->where('id_unit', $idUnit); }
        $tren_ls = $qTLS->groupBy('bulan')->orderBy('bulan')->pluck('nilai','bulan')->toArray();

        $qTGU = Spj::selectRaw('MONTH(tanggal) as bulan, SUM(total) as nilai')
                    ->whereYear('tanggal', $tahun)->where('sumber_dana', 'GU');
        if ($idUnit) { $qTGU->where('id_unit', $idUnit); }
        $tren_gu = $qTGU->groupBy('bulan')->orderBy('bulan')->pluck('nilai','bulan')->toArray();

        $chart_ls = [];
        $chart_gu = [];
        for ($m = 1; $m <= 12; $m++) {
            $chart_ls[] = isset($tren_ls[$m]) ? round($tren_ls[$m] / 1000000, 1) : 0;
            $chart_gu[] = isset($tren_gu[$m]) ? round($tren_gu[$m] / 1000000, 1) : 0;
        }

        /* ── DONUT ── */
        $donut_ls_pct = $total_spj_nilai > 0
                        ? round(($total_ls / $total_spj_nilai) * 100) : 0;
        $donut_gu_pct = 100 - $donut_ls_pct;
        $circum       = 301.6;
        $dash_ls      = round($circum * $donut_ls_pct / 100, 1);
        $dash_gu      = round($circum * $donut_gu_pct / 100, 1);

        /* ── RECENT SPJ ── */
        $qRecent = Spj::with(['anggaran.subKegiatan'])->whereYear('tanggal', $tahun);
        if ($idUnit) { $qRecent->where('id_unit', $idUnit); }
        $recent_spj = $qRecent->latest('tanggal')->take(7)->get();

        /* ── DAFTAR ANGGARAN ── */
        $qAng = Anggaran::with(['subKegiatan'])->where('tahun', $tahun);
        if ($idUnit) { $qAng->where('id_unit', $idUnit); }
        $daftar_anggaran = $qAng->orderBy('realisasi', 'desc')->take(5)->get();

        /* ── AKTIVITAS ── */
        $qAkt = Spj::latest('updated_at');
        if ($idUnit) { $qAkt->where('id_unit', $idUnit); }
        $aktivitas_raw = $qAkt->take(5)->get();

        $aktivitas = [];
        foreach ($aktivitas_raw as $s) {
            $aktivitas[] = [
                'text'  => 'SPJ ' . strtoupper($s->sumber_dana ?? '-')
                         . ' - ' . Str::limit($s->uraian ?? 'Tidak ada uraian', 40),
                'waktu' => Carbon::parse($s->updated_at)->diffForHumans(),
                'tipe'  => strtolower($s->sumber_dana ?? 'ls'),
            ];
        }

        return view('Dashboard.Dashboard_admin', compact(
            'userx', 'tahun',
            'title', 'breadcumd', 'breadcumd1', 'breadcumd2', 'active_home',
            'total_ls', 'total_gu', 'total_spj_nilai', 'total_spj_count',
            'pagu_total', 'realisasi_total', 'sisa_pagu_total', 'persen_realisasi',
            'realisasi_ls', 'realisasi_gu',
            'chart_ls', 'chart_gu', 'bulan_labels',
            'donut_ls_pct', 'donut_gu_pct', 'dash_ls', 'dash_gu', 'circum',
            'recent_spj', 'daftar_anggaran', 'aktivitas'
        ));
    }
}