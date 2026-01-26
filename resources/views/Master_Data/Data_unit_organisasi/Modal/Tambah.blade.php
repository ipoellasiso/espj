<div class="modal fade" id="modalUnit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formUnit" name="formUnit">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah / Edit Unit Organisasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="id">

                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode Unit</label>
                        <input type="text" class="form-control" name="kode" id="kode" placeholder="Contoh: 1.01.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Unit Organisasi</label>
                        <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama unit..." required>
                    </div>

                    <div class="mb-3">
                        <label for="id_bidang" class="form-label">Bidang Urusan</label>
                        <select name="id_bidang" id="id_bidang" class="form-select" required>
                            <option value="">-- Pilih Bidang --</option>
                            @foreach(DB::table('bidang_urusan')->get() as $b)
                                <option value="{{ $b->id }}">{{ $b->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr>
                    <h6 class="fw-bold">Informasi Pejabat</h6>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Kepala</label>
                            <input type="text" name="kepala" id="kepala" class="form-control">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>NIP Kepala</label>
                            <input type="text" name="nip_kepala" id="nip_kepala" class="form-control">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Bendahara</label>
                            <input type="text" name="bendahara" id="bendahara" class="form-control">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>NIP Bendahara</label>
                            <input type="text" name="nip_bendahara" id="nip_bendahara" class="form-control">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>PPK</label>
                            <input type="text" name="ppk" id="ppk" class="form-control">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>NIP PPK</label>
                            <input type="text" name="nip_ppk" id="nip_ppk" class="form-control">
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Alamat</label>
                            <textarea name="alamat" id="alamat" class="form-control"></textarea>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Pejabat Barang</label>
                            <input type="text" name="pejabatbarang" id="pejabatbarang" class="form-control">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>NIP Pejabat Barang</label>
                            <input type="text" name="nip_pejabatbarang" id="nip_pejabatbarang" class="form-control">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Nomor SK</label>
                            <input type="text" name="nomor_sk" id="nomor_sk" class="form-control">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>Tanggal SK</label>
                            <input type="date" name="tanggal_sk" id="tanggal_sk" class="form-control">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Bendahara Barang</label>
                            <input type="text" name="bend_barang" id="bend_barang" class="form-control">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label>NIP Bendahara Barang</label>
                            <input type="text" name="nip_bend_barang" id="nip_bend_barang" class="form-control">
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Keterangan</label>
                            <textarea name="ket" id="ket" class="form-control"></textarea>
                        </div>
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
