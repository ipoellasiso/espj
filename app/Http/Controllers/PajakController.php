<?php

namespace App\Http\Controllers;

use App\Exports\RegisterPajakExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class PajakController extends Controller
{
    public function register()
    {
        return view('Pajak.Register.index');
    }

    public function registerData(Request $request)
    {
        $query = DB::table('spj_pajak as p')
            ->join('spj as s', 's.id', '=', 'p.id_spj')
            ->leftJoin('rekanan as r', 'r.id', '=', 's.id_rekanan')
            ->select(
                's.nomor_spj',
                's.tanggal',
                'r.nama_rekanan',
                'p.jenis_pajak',
                'p.nilai_pajak',
                'p.ebilling',
                'p.ntpn'
            );

        // ✅ FILTER TANGGAL
        if ($request->tgl_awal && $request->tgl_akhir) {
            $query->whereBetween('s.tanggal', [$request->tgl_awal, $request->tgl_akhir]);
        }

        return DataTables::of($query)->make(true);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new RegisterPajakExport($request->tgl_awal, $request->tgl_akhir),
            'register_pajak.xlsx'
        );
    }

}
