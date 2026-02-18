<?php

namespace App\Http\Controllers;

use App\Models\Lpj;
use App\Models\UserModel;
use Illuminate\Support\Facades\Auth;

class PaDashboardController extends Controller
{
    public function index()
    {
         $userx  = UserModel::find(Auth::id());
        $idUnit = $userx->id_unit;
        $tahun  = $userx->tahun ?? date('Y');

        /* ✅ MENUNGGU APPROVAL */
        $lpjMenunggu = Lpj::with('pembuat','ppk','anggaran.subKegiatan')
            ->where('status','disetujui_ppk')
            ->where('periode_tahun',$tahun)
            ->where('id_unit',$idUnit)
            ->latest()
            ->get();

        /* ✅ SUDAH APPROVED */
        $lpjApproved = Lpj::with('pembuat','ppk','anggaran.subKegiatan')
            ->whereIn('status',['approved','generated','selesai'])
            ->where('periode_tahun',$tahun)
            ->where('id_unit',$idUnit)
            ->latest()
            ->get();

        $stat = [
            'menunggu' => $lpjMenunggu->count(),
            'approved' => $lpjApproved->count(),
            'ditolak'  => Lpj::where('status','ditolak_pa')
                            ->where('periode_tahun',$tahun)
                            ->where('id_unit',$idUnit)
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
            'lpjApproved'           => $lpjApproved,
        ];

        return view('pa.dashboard', $data);
    }
}
