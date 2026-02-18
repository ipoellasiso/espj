<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Lpj extends Model
{
    use SoftDeletes;

    protected $table = 'lpj';

    protected $fillable = [
        'nomor_lpj', 'id_anggaran', 'id_unit', 'created_by',
        'jenis', 'periode_bulan', 'periode_tahun',
        'total_spj', 'saldo_awal', 'saldo_akhir',
        'status',
        'id_ppk', 'catatan_ppk', 'tgl_verifikasi',
        'id_pa',  'catatan_pa',  'tgl_approval',
        'tgl_generate', 'file_lpj', 'jumlah_cetak',
        'keterangan',
    ];

    protected $casts = [
        'tgl_verifikasi' => 'datetime',
        'tgl_approval'   => 'datetime',
        'tgl_generate'   => 'datetime',
        'total_spj'      => 'decimal:2',
        'saldo_awal'     => 'decimal:2',
        'saldo_akhir'    => 'decimal:2',
    ];

    /* ═══════════════════════════════
     |  RELASI
    ═══════════════════════════════ */

    /** Satu LPJ punya banyak SPJ */
    public function spjList()
    {
        return $this->hasMany(Spj::class, 'id_lpj', 'id');
    }

    public function anggaran()
    {
        return $this->belongsTo(Anggaran::class, 'id_anggaran', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(UnitOrganisasi::class, 'id_unit', 'id');
    }

    public function pembuat()
    {
        return $this->belongsTo(UserModel::class, 'created_by', 'id');
    }

    public function ppk()
    {
        return $this->belongsTo(UserModel::class, 'id_ppk', 'id');
    }

    public function pa()
    {
        return $this->belongsTo(UserModel::class, 'id_pa', 'id');
    }

    /* ═══════════════════════════════
     |  HELPERS STATUS
    ═══════════════════════════════ */

    /** Label status tampilan */
    public function getLabelStatusAttribute()
    {
        $labels = [
            'draft'          => 'Draft',
            'diajukan'       => 'Diajukan',
            'verifikasi_ppk' => 'Verifikasi PPK',
            'revisi'         => 'Perlu Revisi',
            'disetujui_ppk'  => 'Disetujui PPK',
            'validasi_pa'    => 'Validasi PA/KPA',
            'ditolak_pa'     => 'Ditolak PA/KPA',
            'approved'       => 'Disetujui PA/KPA',
            'generated'      => 'Siap Cetak',
            'selesai'        => 'Selesai',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    /** Warna badge Mazer per status */
    public function getBadgeStatusAttribute()
    {
        $badges = [
            'draft'          => 'secondary',
            'diajukan'       => 'info',
            'verifikasi_ppk' => 'warning',
            'revisi'         => 'danger',
            'disetujui_ppk'  => 'primary',
            'validasi_pa'    => 'warning',
            'ditolak_pa'     => 'danger',
            'approved'       => 'success',
            'generated'      => 'success',
            'selesai'        => 'dark',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    /** Nama bulan Indonesia */
    public function getNamaBulanAttribute()
    {
        $bulan = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
            5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
            9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
        ];
        return $bulan[$this->periode_bulan] ?? '-';
    }

    /** Hitung ulang total dari SPJ yang terhubung */
    public function hitungUlangTotal()
    {
        $total = $this->spjList()->sum('total');
        $this->total_spj   = $total;
        $this->saldo_akhir = $this->saldo_awal - $total;
        $this->save();
        return $total;
    }

    /* ═══════════════════════════════
     |  GENERATE NOMOR LPJ
    ═══════════════════════════════ */

    public static function generateNomor($jenis, $bulan, $tahun, $idUnit = null)
    {
        // Format: LPJ-GU/001/UNIT/BULAN/TAHUN
        $bulanRom = [
            1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',
            7=>'VII',8=>'VIII',9=>'IX',10=>'X',11=>'XI',12=>'XII'
        ];
        $urutan = self::where('jenis', $jenis)
                    ->where('periode_bulan', $bulan)
                    ->where('periode_tahun', $tahun)
                    ->when($idUnit, function($q) use ($idUnit) {
                        $q->where('id_unit', $idUnit);
                    })
                    ->count() + 1;

        return 'LPJ-' . $jenis . '/' . str_pad($urutan, 3, '0', STR_PAD_LEFT)
             . '/' . $bulanRom[$bulan] . '/' . $tahun;
    }

    /* ═══════════════════════════════
     |  SCOPE QUERY
    ═══════════════════════════════ */

    public function scopeDraft($q)          { return $q->where('status', 'draft'); }
    public function scopeMenungguVerif($q)  { return $q->where('status', 'diajukan'); }
    public function scopeMenungguApproval($q){ return $q->where('status', 'disetujui_ppk'); }
    public function scopeApproved($q)       { return $q->whereIn('status', ['approved','generated','selesai']); }
    public function scopeTahun($q, $tahun)  { return $q->where('periode_tahun', $tahun); }
    public function scopeUnit($q, $idUnit)  { return $q->where('id_unit', $idUnit); }
}