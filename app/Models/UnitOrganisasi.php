<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitOrganisasi extends Model
{
    protected $table = 'unit_organisasi';
    protected $fillable = [
        'id_bidang',
        'kode',
        'nama', 
        'ket',
        'kepala',
        'nip_kepala',
        'bendahara',
        'nip_bendahara',
        'pptk',
        'nip_pptk',
        'ppk',
        'nip_ppk',
        'pejabatbarang',
        'nip_pejabatbarang',
        'nomor_sk',
        'tanggal_sk',
        'alamat',
        'bend_barang',
        'nip_bend_barang'
    ];

    public function bidang()
    {
        return $this->belongsTo(BidangUrusan::class, 'id_bidang', 'id');
    }

    public function program()
    {
        return $this->hasMany(Program::class, 'id_unit');
    }
}
