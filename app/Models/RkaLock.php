<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RkaLock extends Model
{
    protected $table = 'rka_lock';

    protected $fillable = [
        'tahap',
        'id_unit',
        'is_active',
        'is_locked'
    ];
}
