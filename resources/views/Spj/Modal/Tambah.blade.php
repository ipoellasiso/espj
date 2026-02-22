<style>
    /* Batasi tinggi area isi modal biar bisa scroll */
    #modalSpj .modal-body {
        max-height: 70vh !important;
        overflow-y: auto !important;
    }
</style>

<div class="modal fade" id="modalSpj" tabindex="-1" aria-labelledby="modalSpjLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalSpjLabel">Tambah / Edit Data SPJ</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form id="formSpj">
        @csrf
        <div class="modal-body">
          <input type="hidden" id="hiddenSpjId">
          <div class="row">
            <div class="col-md-4 mb-2">
                <label>Jenis Kwitansi</label>
                <select name="jenis_kwitansi" id="jenis_kwitansi" class="form-select select2">
                    <option value="">-- Pilih Jenis Kwitansi --</option>
                    {{-- <option value="gj_tpp">Kwitansi Gaji & TPP</option> --}}
                    <option value="pihak_ketiga">Kwitansi Pihak Ketiga</option>
                    <option value="honor_transport">Kwitansi Honor/Insentif</option>
                    {{-- <option value="kontrak">Kwitansi Kontrak</option> --}}
                </select>
            </div>

            <div class="col-md-4 mb-2">
              <label>Pilih RKA</label>
              <select name="id_anggaran" id="selectRka" class="form-select select2" style="width: 100%;" required>
                <option value="">-- Pilih Sub Kegiatan --</option>
                @foreach ($anggaran as $a)
                  <option value="{{ $a->id }}" data-sisapagu="{{ $a->sisa_pagu }}">
                    {{ $a->subKegiatan->kode }} - {{ $a->subKegiatan->nama }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4 mb-2">
              <label>Pilih Rekening Belanja</label>
              <select name="kode_rekening" id="selectRekening" class="form-select select2">
                <option value="">-- Pilih Rekening Belanja --</option>
              </select>
            </div>

            <div class="col-md-4 mb-2">
              <label>Penerima (Rekanan)</label>
              {{-- <select name="id_rekanan" id="selectRekanan" class="form-control select2"> --}}
                  <select name="id_rekanan" id="selectRekanan" class="form-select select2" style="width: 100%;">
                      <option value="">-- Pilih Rekanan --</option>
                  </select>
                  {{-- <option value="">-- Pilih Rekanan --</option>
                  @foreach ($rekanan as $r)
                      <option value="{{ $r->id }}">{{ $r->nama_rekanan }}</option>
                  @endforeach --}}
              </select>
            </div>

            <div class="col-md-4 mb-2">
              <label>Tanggal Kwitansi</label>
              <input type="date" name="tanggal" class="form-control" required>
            </div>

            <div class="col-md-4 mb-2">
              <label>Tanggal Nota Pesanan</label>
              <input type="date" name="tanggal_nope" class="form-control" required>
            </div>

            <div class="col-md-4 mb-2">
                <label>Sumber Dana</label>
                <select name="sumber_dana" id="selectSumberDana" class="form-select select2" style="width: 100%;" required>
                    <option value="">-- Pilih Sumber Dana --</option>
                    <option value="DAU">DAU</option>
                    <option value="DAU GAJI">DAU GAJI</option>
                    <option value="DAU Earmark">DAU Earmark</option>
                    <option value="DAK Fisik">DAK Fisik</option>
                    <option value="DAK Non Fisik">DAK Non Fisik</option>
                    <option value="PAD">PAD</option>
                    <option value="PAD TPP">PAD TPP</option>
                    <option value="DBH Provinsi">DBH Provinsi</option>
                    <option value="DBH Pusat">DBH Pusat</option>
                </select>
            </div>

            <div class="col-md-12 mb-2">
              <label>Uraian</label>
              <textarea name="uraian" class="form-control" rows="2" placeholder="Masukkan uraian..." required></textarea>
            </div>

          </div>

          <div id="formHonor" style="display:none;">
            <hr>
            <h6>Daftar Penerima Honor / Insentif</h6>

            <table class="table table-bordered align-middle" id="tabel-honor">
                <thead class="table-light text-center">
                    <tr>
                        <th>Nama Penerima</th>
                        <th>Jabatan</th>
                        <th>Honor (Rp)</th>
                        <th>Pajak (%)</th>
                        <th>Nilai Pajak (Rp)</th>
                        <th>Diterima (Rp)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody id="tbodyHonor">
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Belum ada data. Tambah penerima dengan tombol di bawah.
                        </td>
                    </tr>
                </tbody>
            </table>

            <strong>Total Pajak: Rp <span id="totalPajakManual">0</span></strong>

            <div class="mt-2">
                <strong>Total Honor: Rp <span id="totalHonorText">0</span></strong><br>
                <strong>Total Pajak: Rp <span id="totalPajakText">0</span></strong><br>
                <strong>Total Dibayarkan: Rp <span id="totalDibayarText">0</span></strong>
            </div>

            <button type="button" id="btnTambahHonor" class="btn btn-sm btn-primary mt-2">
                <i class="bi bi-plus"></i> Tambah Penerima
            </button>
        </div>

          <hr>
          <h6>Rincian Barang</h6>

          <table class="table table-bordered align-middle" id="tabel-rincian">
            <thead class="table-light text-center">
              <tr>
                <th>Nama Barang</th>
                <th>Volume</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="tbodyBarang">
              <tr>
                <td colspan="6" class="text-center text-muted">Pilih RKA dan Rekening Belanja untuk menampilkan rincian barang.</td>
              </tr>
            </tbody>
          </table>

          <div class="text-end mt-3">
            <strong>Total SPJ: Rp <span id="totalSpjText">0</span></strong>
            <div class="progress mt-2" style="height: 18px;">
              <div id="progressPagu" class="progress-bar bg-success" style="width: 0%">0%</div>
            </div>
          </div>

          <hr>
            <h6>Data Pajak</h6>

            <table class="table table-bordered" id="tabel-pajak">
                <thead class="table-light text-center">
                    <tr>
                        <th>Jenis Pajak</th>
                        <th>Nilai Pajak</th>
                        <th>E-Billing</th>
                        <th>NTPN</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodyPajak">
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Belum ada pajak
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="button" class="btn btn-sm btn-primary" id="btnTambahPajak">
                <i class="bi bi-plus"></i> Tambah Pajak
            </button>
            
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary" id="btnSimpanSpj">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>