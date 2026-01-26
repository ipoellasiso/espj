<?php

namespace App\Http\Controllers;

use App\Models\Pptk;
use App\Models\UserModel;
use App\Models\UnitOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PptkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // 🔑 ambil unit dari database
        if ($user->role === 'admin') {
            $unit = UnitOrganisasi::orderBy('nama')->get(); // admin: semua unit
        } else {
            $unit = UnitOrganisasi::where('id', $user->id_unit)->get(); // OPD: unit sendiri
        }

        $data = [
            'title' => 'Data PPTK',
            'active_master_data' => 'active',
            'breadcumd' => 'Pengaturan',
            'breadcumd1' => 'Master Data',
            'breadcumd2' => 'PPTK',
            'userx' => UserModel::where('id', $user->id)->first(),
            'unit'  => $unit, // ✅ TAMBAHKAN INI
        ];

        if ($request->ajax()) {

            $pptk = Pptk::with('unit')
                ->where('id_unit', $user->id_unit)
                ->orderBy('id', 'desc');

            return DataTables::of($pptk)
                ->addIndexColumn()
                ->addColumn('unit', function ($row) {
                    return $row->unit->nama ?? '-';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="javascript:void(0)" 
                        class="editPptk btn btn-primary btn-sm" 
                        data-id="'.$row->id.'">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="javascript:void(0)" 
                        class="deletePptk btn btn-danger btn-sm" 
                        data-id="'.$row->id.'">
                            <i class="bi bi-trash3"></i>
                        </a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Master_Data.Pptk.index', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nip' => 'required',
            'jabatan' => 'required'
        ]);

        Pptk::updateOrCreate(
            [
                'id' => $request->id,
                'id_unit' => Auth::user()->id_unit
            ],
            [
                'nama' => $request->nama,
                'nip' => $request->nip,
                'jabatan' => $request->jabatan
            ]
        );

        return response()->json([
            'success' => 'Data PPTK berhasil disimpan'
        ]);
    }

    public function edit($id)
    {
        $pptk = Pptk::where('id', $id)
            ->where('id_unit', Auth::user()->id_unit)
            ->firstOrFail();

        return response()->json($pptk);
    }

    public function destroy($id)
    {
        Pptk::where('id', $id)
            ->where('id_unit', Auth::user()->id_unit)
            ->delete();

        return response()->json([
            'success' => 'Data PPTK berhasil dihapus'
        ]);
    }
}
