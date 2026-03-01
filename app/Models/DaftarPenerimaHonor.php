<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaftarPenerimaHonor extends Model
{
    protected $table = 'daftar_penerima_honor';

    protected $fillable = [
        'id_spj',
        'nama',
        'jabatan',
        'jumlah',
        'pajak',
        'nilai_pajak',
        'diterima',
        // ✅ BARU
        'nama_bank',
        'no_rekening',
        'ttd'
    ];

    public function spj()
    {
        return $this->belongsTo(Spj::class, 'id_spj');
    }
}
