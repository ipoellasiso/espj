<?php

namespace App\Exports;

use App\Models\Anggaran;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class RkaExport implements FromView
{
    protected $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        $anggaran = Anggaran::with(['rincian', 'subKegiatan.kegiatan.program.bidang.urusan'])->findOrFail($this->id);
        $rincian = $this->buildHierarki($anggaran->rincian);

        return view('laporan.Export.Excel', compact('anggaran', 'rincian'));
    }

    private function buildHierarki($items, $parentId = null)
    {
        $tree = [];
        foreach ($items as $item) {
            if ($item->id_parent == $parentId) {
                $item->children = $this->buildHierarki($items, $item->id);
                $tree[] = $item;
            }
        }
        return $tree;
    }
}
