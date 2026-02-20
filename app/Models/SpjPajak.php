<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpjPajak extends Model
{
    protected $table = 'spj_pajak';

    protected $fillable = [
        'id_spj',
        'jenis_pajak',
        'nilai_pajak',
        'ebilling',
        'ntpn'
    ];

    public function spj()
    {
        return $this->belongsTo(Spj::class, 'id_spj');
    }

}
