<?php

namespace App\Http\Controllers;

use App\Models\Rekanan;
use App\Models\UserModel;
use App\Models\UnitOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class RekananController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // 🔑 Unit logic sama seperti PPTK
        if ($user->role === 'admin') {
            $unit = UnitOrganisasi::orderBy('nama')->get();
        } else {
            $unit = UnitOrganisasi::where('id', $user->id_unit)->get();
        }

        $data = [
            'title' => 'Data Rekanan',
            'active_master_data' => 'active',
            'active_subopd' => 'active',
            'active_rekanan' => 'active',
            'breadcumd' => 'Pengaturan',
            'breadcumd1' => 'Master Data',
            'breadcumd2' => 'Rekanan',
            'userx' => UserModel::where('id', $user->id)->first(),
            'unit'  => $unit,
        ];

        if ($request->ajax()) {

            $rekanan = Rekanan::with('unit')
                ->where('id_unit', $user->id_unit)
                ->orderBy('id', 'desc');

            return DataTables::of($rekanan)
                ->addIndexColumn()

                ->addColumn('unit', function ($row) {
                    return $row->unit->nama ?? '-';
                })

                ->addColumn('action', function ($row) {
                    return '
                        <a href="javascript:void(0)" 
                        class="editRekanan btn btn-primary btn-sm" 
                        data-id="'.$row->id.'">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="javascript:void(0)" 
                        class="deleteRekanan btn btn-danger btn-sm" 
                        data-id="'.$row->id.'">
                            <i class="bi bi-trash3"></i>
                        </a>
                    ';
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Master_Data.Rekanan.index', $data);
    }

    public function store(Request $request)
    {
        try {

            $request->validate([
                'nama_rekanan' => 'required'
            ]);

            if (!empty($request->id)) {

                $rekanan = Rekanan::where('id', $request->id)
                    ->where('id_unit', Auth::user()->id_unit)
                    ->first();

                if (!$rekanan) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data tidak ditemukan / bukan milik OPD'
                    ]);
                }

                $rekanan->update([
                    'nama_rekanan' => $request->nama_rekanan,
                    'alamat' => $request->alamat,
                    'npwp' => $request->npwp,
                    'jabatan' => $request->jabatan,
                    'nip' => $request->nip,
                ]);

            } else {

                Rekanan::create([
                    'nama_rekanan' => $request->nama_rekanan,
                    'alamat' => $request->alamat,
                    'npwp' => $request->npwp,
                    'jabatan' => $request->jabatan,
                    'nip' => $request->nip,
                    'id_unit' => Auth::user()->id_unit
                ]);
            }

            return response()->json([
                'success' => 'Data Rekanan berhasil disimpan'
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()   // 🔥 INI KUNCI DEBUG
            ]);
        }
    }

    public function edit($id)
    {
        $rekanan = Rekanan::where('id', $id)
            ->where('id_unit', Auth::user()->id_unit)
            ->firstOrFail();

        return response()->json($rekanan);
    }

    public function destroy($id)
    {
        Rekanan::where('id', $id)
            ->where('id_unit', Auth::user()->id_unit)
            ->delete();

        return response()->json([
            'success' => 'Data Rekanan berhasil dihapus'
        ]);
    }
}
