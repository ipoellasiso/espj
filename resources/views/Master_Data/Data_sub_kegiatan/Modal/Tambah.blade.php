<div class="modal fade" id="modalSubKegiatan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formSubKegiatan" name="formSubKegiatan">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah / Edit Sub Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="id">

                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode Sub Kegiatan</label>
                        <input type="text" class="form-control" name="kode" id="kode" placeholder="Contoh: 1.01.01.01.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Sub Kegiatan</label>
                        <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama sub kegiatan..." required>
                    </div>

                    <div class="mb-3">
                        <label for="id_kegiatan" class="form-label">Kegiatan</label>
                        <select name="id_kegiatan" id="id_kegiatan" class="form-select" required>
                            <option value="">-- Pilih Kegiatan --</option>
                            @foreach(DB::table('kegiatan')->get() as $k)
                                <option value="{{ $k->id }}">{{ $k->kode }} - {{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" id="saveBtn" class="btn btn-outline-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
