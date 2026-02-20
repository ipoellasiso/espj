<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BidangUrusanController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JenisController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\KelompokController;
use App\Http\Controllers\Landing_pageController;
use App\Http\Controllers\LaporanAnggaranController;
use App\Http\Controllers\LpjController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ObjekController;
use App\Http\Controllers\PaDashboardController;
use App\Http\Controllers\PajakController;
use App\Http\Controllers\PpkDashboardController;
use App\Http\Controllers\PptkController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\RekananController;
use App\Http\Controllers\RincianObjekController;
use App\Http\Controllers\SpjController;
use App\Http\Controllers\SpjPrintController;
use App\Http\Controllers\SubKegiatanController;
use App\Http\Controllers\SubRincianObjekController;
use App\Http\Controllers\UnitOrganisasiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('Tampilan_tambahan.Landing_page');
// });

Route::get('/', [AuthController::class, 'login']);
// Route::get('/', [MaintenanceController::class, 'index']);

// AUTH
Route::get('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/cek_login', [AuthController::class, 'cek_login']);
Route::get('/logout', [AuthController::class, 'logout']);

//DATA USER
// User Management
Route::get('/tampiluser',         [UserController::class, 'index'])->name('user.index')->middleware('auth:web','checkRole:Admin,User');
Route::post('/user/store',        [UserController::class, 'store'])->name('user.store')->middleware('auth:web','checkRole:Admin,User');
Route::get('/user/edit/{id}',     [UserController::class, 'edit'])->name('user.edit')->middleware('auth:web','checkRole:Admin,User');
Route::delete('/user/destroy/{id}', [UserController::class, 'destroy'])->name('user.destroy')->middleware('auth:web','checkRole:Admin,User');
Route::post('/user/nonaktif/{id}', [UserController::class, 'nonaktif'])->name('user.nonaktif')->middleware('auth:web','checkRole:Admin,User');
Route::post('/user/aktif/{id}',   [UserController::class, 'aktif'])->name('user.aktif')->middleware('auth:web','checkRole:Admin,User');
Route::get('/user/opd',           [UserController::class, 'getDataopd'])->name('user.opd')->middleware('auth:web','checkRole:Admin,User');
Route::get('/user/show/{id}', [UserController::class, 'show'])->name('user.show')->middleware('auth:web','checkRole:Admin,User');

// HOME
Route::get('/home', [HomeController::class, 'index'])->middleware('auth:web','checkRole:Admin,User');

// Route::get('/laporan/{id}', [App\Http\Controllers\LaporanAnggaranController::class, 'show'])
//     ->name('laporan.anggaran')->middleware('auth:web','checkRole:Admin,User');
Route::get('/laporan/import', [LaporanAnggaranController::class, 'importPage'])->name('laporan.import')->middleware('auth:web','checkRole:Admin,User');
Route::post('/laporan/import', [LaporanAnggaranController::class, 'importExcel'])->name('laporan.import.post')->middleware('auth:web','checkRole:Admin,User');

// DATA RKA
Route::get('/rka', [LaporanAnggaranController::class, 'index'])->name('rka.index')->middleware('auth:web','checkRole:Admin,User');
Route::get('/rka/data', [LaporanAnggaranController::class, 'getData'])->name('rka.data')->middleware('auth:web','checkRole:Admin,User');
Route::get('/rka/{id}', [LaporanAnggaranController::class, 'show'])->name('rka.show')->middleware('auth:web','checkRole:Admin,User');
Route::post('/rka/store', [LaporanAnggaranController::class, 'store'])->name('rka.store')->middleware('auth:web','checkRole:Admin,User');
Route::delete('/rka/destroy/{id}', [LaporanAnggaranController::class, 'destroy'])->name('rka.destroy')->middleware('auth:web','checkRole:Admin,User');
Route::get('/rka/edit/{id}', [LaporanAnggaranController::class, 'edit'])->name('rka.edit')->middleware('auth:web','checkRole:Admin,User');
Route::get('/rka/{id}', [LaporanAnggaranController::class, 'show'])->name('rka.show')->middleware('auth:web','checkRole:Admin,User');
Route::get('/rka/subkegiatan/select', [LaporanAnggaranController::class, 'getSubKegiatan'])->name('rka.subkegiatan.select')->middleware('auth:web','checkRole:Admin,User');

Route::get('/api/urusan', [LaporanAnggaranController::class, 'getUrusan'])->middleware('auth:web','checkRole:Admin,User');
Route::get('/api/bidang', [LaporanAnggaranController::class, 'getBidang'])->middleware('auth:web','checkRole:Admin,User');
Route::get('/api/program', [LaporanAnggaranController::class, 'getProgram'])->middleware('auth:web','checkRole:Admin,User');
Route::get('/api/kegiatan', [LaporanAnggaranController::class, 'getKegiatan'])->middleware('auth:web','checkRole:Admin,User');
Route::get('/api/sub-kegiatan', [LaporanAnggaranController::class, 'getSubKegiatanByKegiatan'])->middleware('auth:web','checkRole:Admin,User');

// CRUD RINCIAN via AJAX (semua lewat controller yang sama)
Route::post('/rka/{id}/rincian/store', [LaporanAnggaranController::class, 'storeRincian'])->name('rka.rincian.store')->middleware('auth:web','checkRole:Admin,User');
Route::get('/rka/rincian/{id}/edit', [LaporanAnggaranController::class, 'editRincian'])->name('rka.rincian.edit')->middleware('auth:web','checkRole:Admin,User');
Route::delete('/rka/rincian/{id}/destroy', [LaporanAnggaranController::class, 'deleteRincian'])->name('rka.rincian.destroy')->middleware('auth:web','checkRole:Admin,User');

Route::get('/rka/{id}/export/pdf', [LaporanAnggaranController::class, 'exportPdf'])->name('rka.export.pdf')->middleware('auth:web','checkRole:Admin,User');
Route::get('/rka/{id}/export/excel', [LaporanAnggaranController::class, 'exportExcel'])->name('rka.export.excel')->middleware('auth:web','checkRole:Admin,User');   

Route::post('/rka/{id}/rincian/import', [LaporanAnggaranController::class, 'importRincian'])->name('rka.rincian.import')->middleware('auth:web','checkRole:Admin,User');

Route::get('/rka/rincian/template', [LaporanAnggaranController::class, 'downloadTemplate'])->name('rka.rincian.template')->middleware('auth:web','checkRole:Admin,User');

Route::get('/rka/{id}/pagu', [LaporanAnggaranController::class, 'getTotalPagu'])->name('rka.getPagu')->middleware('auth:web','checkRole:Admin,User');

// Data Master Bidang
Route::get('/bidang-urusan', [BidangUrusanController::class, 'index'])->name('bidang-urusan.index')->middleware('auth:web','checkRole:Admin');
Route::post('/bidang-urusan/store', [BidangUrusanController::class, 'store'])->name('bidang-urusan.store')->middleware('auth:web','checkRole:Admin');
Route::get('/bidang-urusan/edit/{id}', [BidangUrusanController::class, 'edit'])->name('bidang-urusan.edit')->middleware('auth:web','checkRole:Admin');
Route::delete('/bidang-urusan/destroy/{id}', [BidangUrusanController::class, 'destroy'])->name('bidang-urusan.destroy')->middleware('auth:web','checkRole:Admin');
Route::post('/bidang-urusan/import', [BidangUrusanController::class, 'importExcel'])->name('bidang-urusan.import')->middleware('auth:web','checkRole:Admin');
Route::get('/bidang-urusan/template', [BidangUrusanController::class, 'downloadTemplate'])->name('bidang-urusan.template')->middleware('auth:web','checkRole:Admin');

// Data Master Unit
Route::middleware(['auth:web', 'checkRole:Admin'])->group(function () {
    Route::get('/unit-organisasi', [UnitOrganisasiController::class, 'index'])->name('unit-organisasi.index');
    Route::post('/unit-organisasi/store', [UnitOrganisasiController::class, 'store']);
    Route::get('/unit-organisasi/edit/{id}', [UnitOrganisasiController::class, 'edit']);
    Route::delete('/unit-organisasi/destroy/{id}', [UnitOrganisasiController::class, 'destroy']);
    Route::post('/unit-organisasi/import', [UnitOrganisasiController::class, 'importExcel'])->name('unit-organisasi.import');
    Route::get('/unit-organisasi/template', [UnitOrganisasiController::class, 'downloadTemplate'])->name('unit-organisasi.template');
    Route::get('/unit-organisasi/export', [UnitOrganisasiController::class, 'exportExcel'])->name('unit-organisasi.export');
});

// Data Master Program
Route::middleware(['auth:web', 'checkRole:Admin'])->group(function () {
    Route::get('/program', [ProgramController::class, 'index'])->name('program.index');
    Route::post('/program/store', [ProgramController::class, 'store']);
    Route::get('/program/edit/{id}', [ProgramController::class, 'edit']);
    Route::delete('/program/destroy/{id}', [ProgramController::class, 'destroy']);
    Route::post('/program/import', [ProgramController::class, 'importExcel'])->name('program.import');
    Route::get('/program/template', [ProgramController::class, 'downloadTemplate'])->name('program.template');
    Route::get('/program/export', [ProgramController::class, 'exportExcel'])->name('program.export');
});

// Data Master Kegiatan
Route::middleware(['auth:web', 'checkRole:Admin'])->group(function () {
    Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index');
    Route::post('/kegiatan/store', [KegiatanController::class, 'store']);
    Route::get('/kegiatan/edit/{id}', [KegiatanController::class, 'edit']);
    Route::delete('/kegiatan/destroy/{id}', [KegiatanController::class, 'destroy']);
    Route::post('/kegiatan/import', [KegiatanController::class, 'importExcel'])->name('kegiatan.import');
    Route::get('/kegiatan/template', [KegiatanController::class, 'downloadTemplate'])->name('kegiatan.template');
    Route::get('/kegiatan/export', [KegiatanController::class, 'exportExcel'])->name('kegiatan.export');
});

// Data Master Sub Kegiatan
Route::middleware(['auth:web', 'checkRole:Admin'])->group(function () {
    Route::get('/sub-kegiatan', [SubKegiatanController::class, 'index'])->name('sub-kegiatan.index');
    Route::post('/sub-kegiatan/store', [SubKegiatanController::class, 'store']);
    Route::get('/sub-kegiatan/edit/{id}', [SubKegiatanController::class, 'edit']);
    Route::delete('/sub-kegiatan/destroy/{id}', [SubKegiatanController::class, 'destroy']);
    Route::post('/sub-kegiatan/import', [SubKegiatanController::class, 'importExcel'])->name('sub-kegiatan.import');
    Route::get('/sub-kegiatan/template', [SubKegiatanController::class, 'downloadTemplate'])->name('sub-kegiatan.template');
    Route::get('/sub-kegiatan/export', [SubKegiatanController::class, 'exportExcel'])->name('sub-kegiatan.export');
});

// Data Master Akun
Route::middleware(['auth:web', 'checkRole:Admin'])->group(function () {
    Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
    Route::post('/akun/store', [AkunController::class, 'store']);
    Route::get('/akun/edit/{id}', [AkunController::class, 'edit']);
    Route::delete('/akun/destroy/{id}', [AkunController::class, 'destroy']);
    Route::post('/akun/import', [AkunController::class, 'importExcel'])->name('akun.import');
    Route::get('/akun/template', [AkunController::class, 'downloadTemplate'])->name('akun.template');
    Route::get('/akun/export', [AkunController::class, 'exportExcel'])->name('akun.export');
});

// Data Master Kelompok
Route::middleware(['auth:web', 'checkRole:Admin'])->group(function () {
    Route::get('/kelompok', [KelompokController::class, 'index'])->name('kelompok.index');
    Route::post('/kelompok/store', [KelompokController::class, 'store']);
    Route::get('/kelompok/edit/{id}', [KelompokController::class, 'edit']);
    Route::delete('/kelompok/destroy/{id}', [KelompokController::class, 'destroy']);
    Route::post('/kelompok/import', [KelompokController::class, 'importExcel'])->name('kelompok.import');
    Route::get('/kelompok/template', [KelompokController::class, 'downloadTemplate'])->name('kelompok.template');
    Route::get('/kelompok/export', [KelompokController::class, 'exportExcel'])->name('kelompok.export');
});

// Data Master Jenis
Route::middleware(['auth:web', 'checkRole:Admin'])->group(function () {
    Route::get('/jenis', [JenisController::class, 'index'])->name('jenis.index');
    Route::post('/jenis/store', [JenisController::class, 'store']);
    Route::get('/jenis/edit/{id}', [JenisController::class, 'edit']);
    Route::delete('/jenis/destroy/{id}', [JenisController::class, 'destroy']);
    Route::post('/jenis/import', [JenisController::class, 'importExcel'])->name('jenis.import');
    Route::get('/jenis/template', [JenisController::class, 'downloadTemplate'])->name('jenis.template');
    Route::get('/jenis/export', [JenisController::class, 'exportExcel'])->name('jenis.export');
});

// Data Master Objek
Route::middleware(['auth:web', 'checkRole:Admin'])->group(function () {
    Route::get('/objek', [ObjekController::class, 'index'])->name('objek.index');
    Route::post('/objek/store', [ObjekController::class, 'store']);
    Route::get('/objek/edit/{id}', [ObjekController::class, 'edit']);
    Route::delete('/objek/destroy/{id}', [ObjekController::class, 'destroy']);
    Route::post('/objek/import', [ObjekController::class, 'importExcel'])->name('objek.import');
    Route::get('/objek/template', [ObjekController::class, 'downloadTemplate'])->name('objek.template');
    Route::get('/objek/export', [ObjekController::class, 'exportExcel'])->name('objek.export');
});

// Data Master Rincian Objek
Route::middleware(['auth:web', 'checkRole:Admin'])->group(function () {
    Route::get('/rincian_objek', [RincianObjekController::class, 'index'])->name('rincian_objek.index');
    Route::post('/rincian_objek/store', [RincianObjekController::class, 'store']);
    Route::get('/rincian_objek/edit/{id}', [RincianObjekController::class, 'edit']);
    Route::delete('/rincian_objek/destroy/{id}', [RincianObjekController::class, 'destroy']);
    Route::post('/rincian_objek/import', [RincianObjekController::class, 'importExcel'])->name('rincian_objek.import');
    Route::get('/rincian_objek/template', [RincianObjekController::class, 'downloadTemplate'])->name('rincian_objek.template');
    Route::get('/rincian_objek/export', [RincianObjekController::class, 'exportExcel'])->name('rincian_objek.export');
});

// Data Master Sub Rincian Objek
Route::middleware(['auth:web', 'checkRole:Admin'])->group(function () {
    Route::get('/sub_rincian_objek', [SubRincianObjekController::class, 'index'])->name('sub_rincian_objek.index');
    Route::post('/sub_rincian_objek/store', [SubRincianObjekController::class, 'store']);
    Route::get('/sub_rincian_objek/edit/{id}', [SubRincianObjekController::class, 'edit']);
    Route::delete('/sub_rincian_objek/destroy/{id}', [SubRincianObjekController::class, 'destroy']);
    Route::post('/sub_rincian_objek/import', [SubRincianObjekController::class, 'importExcel'])->name('sub_rincian_objek.import');
    Route::get('/sub_rincian_objek/template', [SubRincianObjekController::class, 'downloadTemplate'])->name('sub_rincian_objek.template');
    Route::get('/sub_rincian_objek/export', [SubRincianObjekController::class, 'exportExcel'])->name('sub_rincian_objek.export');
});

Route::middleware(['auth:web', 'checkRole:User'])->group(function () {
    Route::resource('/spj', SpjController::class);
    Route::get('spj/print/{id}', [SpjController::class, 'print'])->name('spj.print');
    Route::get('/spj/get-rincian/{id_anggaran}', [SpjController::class, 'getRincian']);
    Route::post('/spj/store', [SpjController::class, 'store'])->name('spj.store');
    Route::get('/spj/{id}/edit', [SpjController::class, 'edit'])->name('spj.edit');
    Route::put('/spj/{id}', [SpjController::class, 'update'])->name('spj.update');

    Route::get('/rekanan/list', [SpjController::class, 'getRekanan'])->name('rekanan.list');

    Route::get('/spj/cetak-resmi/{id}', [App\Http\Controllers\SpjPrintController::class, 'cetakResmi'])->name('spj.cetak.resmi');
    Route::get('/spj/get-rincian/{id}', [SpjController::class, 'getRincian']);
    Route::delete('/spj/{id}', [SpjController::class, 'destroy'])->name('spj.destroy');
    Route::get('/spj/get-rekening/{id_anggaran}', [SpjController::class, 'getRekening']);
    Route::get('/spj/get-rincian/{id_anggaran}/{id_rekening}', [SpjController::class, 'getRincianselect2']);
    Route::get('/spj/next/get-numbers', [SpjController::class, 'getNextNumbers'])->name('spj.getNextNumbers');
    

    Route::prefix('spj/cetak')->group(function () {
        Route::get('/spj/cetak/kwitansi-pdf/{id}', [SpjController::class, 'cetakKwitansiPdf'])->name('spj.cetak.kwitansi.pdf');
        Route::get('/spj/cetak/nota/{id}', [SpjController::class, 'cetakNota'])->name('spj.cetak.nota');
        Route::get('/spj/{id}/berita-acara', [SpjController::class, 'cetakBeritaAcara'])->name('spj.cetak.bapp');
        Route::get('/spj/{id}/cetak-bapb', [SpjController::class, 'cetakBapb'])->name('spj.cetak.bapb');
        Route::get('/spj/cetak/ba-penyerahan/{id}', [SpjController::class, 'cetakBAPenyerahan'])->name('spj.cetak.ba_penyerahan');
        Route::get('/spj/cetak/penerimaan/{id}', [SpjController::class, 'cetakPenerimaan'])->name('spj.cetak.penerimaan');
        // Route::get('/bapp/{id}', [SpjPrintController::class, 'cetakBAPP'])->name('spj.cetak.bapp');
        // Route::get('/bapb/{id}', [SpjPrintController::class, 'cetakBAPB'])->name('spj.cetak.bapb');
        // Route::get('/bast/{id}', [SpjPrintController::class, 'cetakBAST'])->name('spj.cetak.bast');
        // Route::get('/penerimaan/{id}', [SpjPrintController::class, 'cetakPenerimaan'])->name('spj.cetak.penerimaan');
        Route::get('/spj/cetak-honor/{id}', [SpjController::class, 'cetakHonor'])->name('spj.cetak.honor');
    });

    Route::post('/rka/tahap/set-active', [LaporanAnggaranController::class, 'setActive'])->name('rka.setActive');
    Route::post('/rka/tahap/toggle-lock', [LaporanAnggaranController::class, 'toggleLock'])->name('rka.toggleLock');

    Route::get('/spj/rka/list', [SpjController::class, 'getRkaForSpj']);
    
});

Route::prefix('pptk')->group(function () {
    Route::get('/pptk', [PptkController::class, 'index'])->name('pptk.index')->middleware('auth:web','checkRole:User');
    Route::post('/store', [PptkController::class, 'store'])->middleware('auth:web','checkRole:User');
    Route::get('/edit/{id}', [PptkController::class, 'edit'])->middleware('auth:web','checkRole:User');
    Route::delete('/destroy/{id}', [PptkController::class, 'destroy'])->middleware('auth:web','checkRole:User');
});

Route::get('/debug/kegiatan/{id}', function ($id) {
    return \App\Models\Kegiatan::find($id);
});

//DATA REKANAN
Route::get('/rekanan', [RekananController::class, 'index'])->name('rekanan.index')->middleware('auth:web','checkRole:User');
Route::post('/rekanan/store', [RekananController::class, 'store'])->middleware('auth:web','checkRole:User');
Route::get('/rekanan/edit/{id}', [RekananController::class, 'edit'])->middleware('auth:web','checkRole:User');
Route::delete('/rekanan/destroy/{id}', [RekananController::class, 'destroy'])->middleware('auth:web','checkRole:User');

/* ──────────────────────────────────────
    |  LPJ — Laporan Pertanggungjawaban
────────────────────────────────────── */
Route::prefix('lpj')->name('lpj.')->group(function () {

    // CRUD Dasar
    Route::get('/',[App\Http\Controllers\LpjController::class, 'index'])->name('index')->middleware('auth:web','checkRole:User');
    Route::get('/create',[App\Http\Controllers\LpjController::class, 'create'])->name('create')->middleware('auth:web','checkRole:User');
    Route::post('/',[App\Http\Controllers\LpjController::class, 'store'])->name('store')->middleware('auth:web','checkRole:User');
    Route::get('/{id}',[App\Http\Controllers\LpjController::class, 'show'])->name('show');
    Route::delete('/{id}', [App\Http\Controllers\LpjController::class, 'destroy'])->name('destroy')->middleware('auth:web','checkRole:User');

    // Alur Status
    Route::post('/{id}/ajukan',[App\Http\Controllers\LpjController::class, 'ajukan'])->name('ajukan')->middleware('auth:web','checkRole:User');

    // Verifikasi PPK
    Route::get( '/{id}/verifikasi-ppk',[App\Http\Controllers\LpjController::class, 'formVerifikasiPpk'])->name('verifikasi_ppk.form');
    Route::post('/{id}/verifikasi-ppk',[App\Http\Controllers\LpjController::class, 'prosesVerifikasiPpk'])->name('verifikasi_ppk.proses');
    Route::post('/{id}/unverifikasi-ppk', [LpjController::class, 'unverifikasiPpk'])->name('unverifikasi_ppk');

    // Approval PA/KPA
    Route::get( '/{id}/approval-pa',[App\Http\Controllers\LpjController::class, 'formApprovalPa'])->name('approval_pa.form');
    Route::post('/{id}/approval-pa',    [App\Http\Controllers\LpjController::class, 'prosesApprovalPa'])->name('approval_pa.proses');
    Route::post('/{id}/unapproval-pa', [LpjController::class, 'unapprovalPa'])->name('unapproval_pa');

    // Manajemen SPJ dalam LPJ
    Route::post('/{id}/tambah-spj',[App\Http\Controllers\LpjController::class, 'tambahSpj'])->name('tambah_spj')->middleware('auth:web','checkRole:User');
    Route::delete('/{id}/hapus-spj/{idSpj}',[App\Http\Controllers\LpjController::class, 'hapusSpj'])->name('hapus_spj')->middleware('auth:web','checkRole:User');

    // Generate & Cetak PDF
    Route::post('/{id}/generate',[App\Http\Controllers\LpjController::class, 'generate'])->name('generate')->middleware('auth:web','checkRole:User');
    Route::get( '/{id}/cetak',[App\Http\Controllers\LpjController::class, 'cetak'])->name('cetak')->middleware('auth:web','checkRole:User');
    Route::get( '/{id}/preview',[App\Http\Controllers\LpjController::class, 'preview'])->name('preview')->middleware('auth:web','checkRole:User');
});

    Route::get('/ppk/dashboard',[PpkDashboardController::class,'index'])->name('ppk.dashboard')->middleware('auth:web','checkRole:Ppk');
    Route::get('/verlpj',[PpkDashboardController::class,'index'])->name('ppk.dashboard')->middleware('auth:web','checkRole:Ppk');

    Route::get('/pa/dashboard',[PaDashboardController::class,'index'])->name('pa.dashboard')->middleware('auth:web','checkRole:Pa');
    

    Route::get('/pajak/register', [PajakController::class, 'register'])->name('pajak.register');
    Route::get('/pajak/register/data', [PajakController::class, 'registerData']);
    Route::get('/pajak/register/export', [PajakController::class, 'exportExcel'])->name('pajak.export');