<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RegisterPajakExport implements FromCollection, WithHeadings
{
    protected $awal, $akhir, $unit;

    public function __construct($awal, $akhir, $unit)
    {
        $this->awal = $awal;
        $this->akhir = $akhir;
        $this->unit  = $unit;
    }

    public function collection()
    {
        $query = DB::table('spj_pajak as p')
            ->join('spj as s', 's.id', '=', 'p.id_spj')
            ->leftJoin('rekanan as r', 'r.id', '=', 's.id_rekanan')
            ->where('s.id_unit', $this->unit)
            ->select(
                's.nomor_spj',
                's.tanggal',
                'r.nama_rekanan',
                'p.jenis_pajak',
                'p.nilai_pajak',
                'p.ebilling',
                'p.ntpn'
            );

        if ($this->awal && $this->akhir) {
            $query->whereBetween('s.tanggal', [$this->awal, $this->akhir]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nomor SPJ',
            'Tanggal',
            'Rekanan',
            'Jenis Pajak',
            'Nilai Pajak',
            'E-Billing',
            'NTPN'
        ];
    }
}