<div class="modal fade" id="modalPptk">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formPptk">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah / Edit PPTK</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="id">

                    <div class="mb-2">
                        <label>Unit</label>
                        <select name="id_unit" id="id_unit" class="form-select" required>
                            <option value="">-- Pilih Unit --</option>
                            @foreach($unit as $u)
                                <option value="{{ $u->id }}">{{ $u->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>NIP</label>
                        <input type="text" name="nip" id="nip" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" id="jabatan" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button class="btn btn-outline-primary" id="saveBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
