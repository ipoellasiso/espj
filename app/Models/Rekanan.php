<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekanan extends Model
{
    use HasFactory;
    protected $table = 'rekanan'; // ← sesuai tabel kamu
    public $timestamps = false;
    protected $fillable = [
        'nama_rekanan',   // 🔥 WAJIB ADA INI
        'alamat',
        'npwp',
        'jabatan',
        'nip',
        'id_unit'
    ];

    public function unit()
    {
        return $this->belongsTo(UnitOrganisasi::class, 'id_unit');
    }
    
}
