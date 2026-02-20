<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spj extends Model
{
    use HasFactory;

    protected $table = 'spj';

    protected $fillable = [
        'id_anggaran',
        'nomor_spj',
        'tanggal',
        'uraian',
        'total',
        'id_unit',
        'nomor_kwitansi',
        'nomor_nota',
        'nomor_bapp',
        'nomor_bapb',
        'nomor_bast',
        'nomor_ba_penerimaan',
        'nama_penerima',
        'id_rekanan',
        'tanggal_nope',
        'jenis_kwitansi',
        'sumber_dana',
        'jenis_pajak',
        'nilai_pajak',
        'ebilling',
        'ntpn'
    ];

    /**
     * Relasi ke Anggaran (RKA)
     */
    public function anggaran()
    {
        return $this->belongsTo(Anggaran::class, 'id_anggaran', 'id');
    }

    /**
     * Relasi ke Detail SPJ
     */
    public function details()
    {
        return $this->hasMany(SpjDetail::class, 'id_spj', 'id');
    }

    public function rekanan()
    {
        return $this->belongsTo(\App\Models\Rekanan::class, 'id_rekanan');
    }

    public function daftarHonor()
    {
        return $this->hasMany(\App\Models\DaftarPenerimaHonor::class, 'id_spj');
    }

    public function pajak()
    {
        return $this->hasMany(SpjPajak::class, 'id_spj');
    }
    
}
