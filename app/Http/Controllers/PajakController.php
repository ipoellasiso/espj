<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RegisterPajakExport;
use App\Models\UserModel;

class PajakController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $data = [
            'title'                 => 'Register Pajak',
            'active_penerimaan'     => 'active',
            'active_sub'            => 'active',
            'active_side_regpajak'  => 'active',
            'breadcumd'             => 'Penatausahaan',
            'breadcumd1'            => 'Register',
            'breadcumd2'            => 'Pajak',
            'userx' => UserModel::where('id', $user->id)->first(),
        ];

        if ($request->ajax()) {

            $query = DB::table('spj_pajak as p')
                ->join('spj as s', 's.id', '=', 'p.id_spj')
                ->leftJoin('rekanan as r', 'r.id', '=', 's.id_rekanan')

                // 🔥 FILTER UNIT USER LOGIN
                ->where('s.id_unit', $user->id_unit)

                ->select(
                    's.nomor_spj',
                    's.tanggal',
                    'r.nama_rekanan',
                    'p.jenis_pajak',
                    'p.nilai_pajak',
                    'p.ebilling',
                    'p.ntpn',
                    'r.npwp'
                );

            // ✅ FILTER TANGGAL
            if ($request->tgl_awal && $request->tgl_akhir) {
                $query->whereBetween('s.tanggal', [$request->tgl_awal, $request->tgl_akhir]);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('nilai_pajak', function ($row) {
                    return number_format($row->nilai_pajak, 2, ',', '.');
                })
                ->make(true);
        }

        return view('Pajak.Register.index', $data);
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();

        return Excel::download(
            new RegisterPajakExport(
                $request->tgl_awal,
                $request->tgl_akhir,
                $user->id_unit   // 🔥 WAJIB
            ),
            'register_pajak.xlsx'
        );
    }
}
