<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpjDetail extends Model
{
    use HasFactory;

    protected $table = 'spj_detail';

    protected $fillable = [
        'id_spj',
        'id_rincian_anggaran',
        'nama_barang',
        'volume',
        'satuan',
        'harga',
        'jumlah',

         // 🔥 PAJAK MANUAL
        'jenis_pajak',
        'nilai_pajak',
        'ebilling',
        'ntpn'
    ];

    /**
     * Relasi ke SPJ
     */
    public function spj()
    {
        return $this->belongsTo(Spj::class, 'id_spj', 'id');
    }

    /**
     * Relasi ke rincian anggaran
     */
    public function rincian()
    {
        return $this->belongsTo(RincianAnggaran::class, 'id_rincian_anggaran', 'id');
    }
}
