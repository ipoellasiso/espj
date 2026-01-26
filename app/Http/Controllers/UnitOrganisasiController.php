<?php

namespace App\Http\Controllers;

use App\Exports\UnitOrganisasiExport;
use App\Models\UserModel;
use App\Models\UnitOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UnitOrganisasiImport;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UnitOrganisasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;
        $data = [
            'title'              => 'Data Unit Organisasi',
            'active_master_data' => 'active',
            'active_unit'        => 'active',
            'breadcumd'          => 'Pengaturan',
            'breadcumd1'         => 'Master Data',
            'breadcumd2'         => 'Unit Organisasi',
            'userx'              => UserModel::where('id', $userId)->first(['fullname','role','gambar','tahun']),
        ];

        if ($request->ajax()) {
            $dataUnit = DB::table('unit_organisasi')
                ->join('bidang_urusan', 'unit_organisasi.id_bidang', '=', 'bidang_urusan.id')
                ->select('unit_organisasi.id', 'unit_organisasi.kode', 'unit_organisasi.nama', 'bidang_urusan.nama as nama_bidang')
                ->orderBy('unit_organisasi.kode', 'asc')
                ->get();

            return DataTables::of($dataUnit)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    return '
                        <a href="javascript:void(0)" class="editUnit btn btn-primary btn-sm" data-id="'.$row->id.'">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="javascript:void(0)" class="deleteUnit btn btn-danger btn-sm" data-id="'.$row->id.'">
                            <i class="bi bi-trash3"></i>
                        </a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Master_Data.Data_unit_organisasi.index', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required',
            'nama' => 'required',
            'id_bidang' => 'required'
        ]);

        $data = $request->only([
            'kode',
            'nama',
            'id_bidang',
            'ket',
            'kepala',
            'nip_kepala',
            'bendahara',
            'nip_bendahara',
            'ppk',
            'nip_ppk',
            'alamat',
            'pejabatbarang',
            'nip_pejabatbarang',
            'nomor_sk',
            'tanggal_sk',
            'bend_barang',
            'nip_bend_barang'
        ]);

        if ($request->id) {
            UnitOrganisasi::find($request->id)->update($data);
            return response()->json(['success' => 'Data berhasil diperbarui!']);
        } else {
            UnitOrganisasi::create($data);
            return response()->json(['success' => 'Data berhasil disimpan!']);
        }
    }

    public function edit($id)
    {
        $data = UnitOrganisasi::find($id);
        return response()->json($data);
    }

    public function destroy($id)
    {
        UnitOrganisasi::find($id)->delete();
        return response()->json(['success' => 'Data berhasil dihapus!']);
    }

    // === IMPORT EXCEL ===
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new UnitOrganisasiImport, $request->file('file_excel'));
            return response()->json(['success' => 'Data berhasil diimport!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal import: '.$e->getMessage()]);
        }
    }

    // === DOWNLOAD TEMPLATE ===
    public function downloadTemplate()
    {
        $filename = 'template_unit_organisasi.xlsx';
        $folderPath = public_path('template');
        $path = $folderPath . '/' . $filename;

        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        if (!File::exists($path)) {
            $headers = ['kode', 'nama', 'id_bidang'];
            $rows = [
                ['1.01.01', 'Subbag Perencanaan', 1],
                ['1.01.02', 'Subbag Keuangan', 1],
            ];

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray($headers, NULL, 'A1');
            $sheet->fromArray($rows, NULL, 'A2');

            $writer = new Xlsx($spreadsheet);
            $writer->save($path);
        }

        return response()->download($path);
    }

    public function exportExcel()
    {
        $filename = 'Data_Unit_Organisasi_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new UnitOrganisasiExport, $filename);
    }

}
