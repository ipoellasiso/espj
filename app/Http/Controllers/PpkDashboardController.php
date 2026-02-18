<?php

namespace App\Http\Controllers;

use App\Models\Lpj;
use App\Models\UserModel;
use Illuminate\Support\Facades\Auth;

class PpkDashboardController extends Controller
{
    public function index()
    {
        $userx  = UserModel::find(Auth::id());
        $idUnit = $userx->id_unit;
        $tahun  = $userx->tahun ?? date('Y');

        /* ✅ LPJ MENUNGGU VERIFIKASI */
        $lpjMenunggu = Lpj::with('pembuat','anggaran.subKegiatan')
            ->whereIn('status',['diajukan','disetujui_ppk'])
            ->where('periode_tahun',$tahun)
            ->where('id_unit',$idUnit)
            ->latest()
            ->get();

        $stat = [
            'menunggu' => $lpjMenunggu->count(),

            'disetujui'=> Lpj::where('status','disetujui_ppk')
                            ->where('id_unit',$idUnit)
                            ->where('periode_tahun',$tahun)
                            ->count(),

            'revisi'=> Lpj::where('status','revisi')
                        ->where('id_unit',$idUnit)
                        ->where('periode_tahun',$tahun)
                        ->count(),
        ];

        $data = [
            'title'                 => 'Dashboard',
            'active_penerimaan'     => 'active',
            'active_sub'            => 'active',
            'active_side_datalpj'   => 'active',
            'breadcumd'             => 'Home',
            'breadcumd1'            => 'Dashboard',
            'breadcumd2'            => 'PPK',
            'userx'                 => $userx,
            'lpjMenunggu'           => $lpjMenunggu,
            'stat'                  => $stat,
        ];


        return view('ppk.dashboard',$data);
    }
}
