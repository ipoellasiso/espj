        <script>
        $(document).ready(function () {
            console.log("SPJ Modal Loaded");

            let isEditMode = false;
            let sisaPaguAktif = 0;

            // === TABEL SPJ ===
            let table = $('#tabelSpj').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('spj.index') }}",
                columns: [
                    { data: 'DT_RowIndex', className: 'text-center' },
                    { data: 'nomor_spj' },
                    { data: 'tanggal' },
                    
                    // === KOLUM URAIAN DIWRAP ===
                    { data: 'uraian', className: 'wrap-uraian' },

                    { data: 'total', render: $.fn.dataTable.render.number('.', ',', 2, 'Rp ') },
                    { data: 'id_unit' },
                    { data: 'aksi', className: 'text-center', orderable: false }
                ]
            });

            // === TOMBOL TAMBAH ===
            $(document).on('click', '#createSpj', function () {
                isEditMode = false;

                $('#formSpj')[0].reset();
                $('#hiddenSpjId').val('');
                $('#modalSpjLabel').text('Tambah Data SPJ');

                $('#selectRka').val('').trigger('change');
                $('#selectRekening').empty().append('<option value="">-- Pilih Rekening --</option>');

                $('#tabel-rincian tbody').html(`
                    <tr><td colspan="6" class="text-center text-muted">Pilih RKA untuk memulai.</td></tr>
                `);

                $('#totalSpjText').text('0');
                $('#progressPagu').css('width', '0%').text('0%');

                $('#formSpj').attr('action', "{{ route('spj.store') }}");
                $('#formSpj').attr('method', 'POST');

                $('#modalSpj').modal('show');
            });

            // === TOMBOL EDIT ===
            $(document).on('click', '.editSpj', function () {

                let id = $(this).data('id');
                isEditMode = true;

                $('#modalSpjLabel').text('Edit Data SPJ');
                $('#modalSpj').modal('show');

                $.ajax({
                    url: `/spj/${id}/edit`,
                    type: 'GET',
                    success: function(res) {
                        if (!res.success) {
                            Swal.fire('Error', res.message, 'error');
                            return;
                        }

                        let spj = res.data;

                        // === SET FIELD FORM ===
                        $('#hiddenSpjId').val(spj.id);
                        $('input[name="tanggal"]').val(spj.tanggal);
                        $('textarea[name="uraian"]').val(spj.uraian);

                        // === SET RKA ===
                        $('#selectRka').val(spj.id_anggaran).trigger('change');

                        $('#selectRekanan').val(spj.id_rekanan).trigger('change');  // 🔥 WAJIB

                        // === Isi rekening tanpa trigger change ===
                        let rekeningSelect = $('#selectRekening');
                        rekeningSelect.empty().append('<option value="">-- Pilih Rekening Belanja --</option>');

                        res.rekening.forEach(r => {
                            rekeningSelect.append(`<option value="${r.kode_rekening}">${r.kode_rekening}</option>`);
                        });

                        // SET rekening tanpa trigger change
                        $('#selectRekening').val(spj.kode_rekening);

                        // === TAMPILKAN DETAIL SPJ ===
                        let tbody = $('#tabel-rincian tbody');
                        tbody.empty();

                        if (spj.details.length > 0) {
                            spj.details.forEach(d => {
                                tbody.append(`
                                    <tr>
                                        <td>
                                            <input type="hidden" name="id_rincian_anggaran[]" value="${d.id_rincian_anggaran}">
                                            <input type="text" name="nama_barang[]" class="form-control" value="${d.nama_barang}">
                                        </td>
                                        <td>
                                            <input type="hidden" name="sisa[]" value="${d.volume}">
                                            <input type="number" name="volume[]" class="form-control volume" value="${d.volume}">
                                            <input type="number" 
                                                        name="volume[]" 
                                                        class="form-control volume" 
                                                        value="0"
                                                        title="Sisa volume: ${d.volume}" 
                                                        data-bs-toggle="tooltip">
                                        </td>
                                        <td><input type="text" name="satuan[]" class="form-control" value="${d.satuan}"></td>
                                        <td><input type="number" name="harga[]" class="form-control harga" value="${d.harga}"></td>
                                        <td class="jumlah text-end" data-value="0">0</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm hapusBaris">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `);
                            });
                        }

                        hitungTotalSPJ();

                        // Set action method PUT
                        $('#formSpj').attr('action', `/spj/${spj.id}`);
                        $('#formSpj').attr('method', 'PUT');
                    }
                });
            });

            // === 🔹 TOMBOL HAPUS ===
            $(document).on('click', '.hapusSpj', function() {
                let id = $(this).data('id');
                console.log("🗑 Menghapus ID:", id);

                Swal.fire({
                    title: 'Yakin menghapus SPJ ini?',
                    text: "Data akan dihapus permanen dan anggaran dikembalikan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/spj/${id}`,
                            type: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Berhasil!', response.message, 'success');
                                    $('#tabelSpj').DataTable().ajax.reload();
                                } else {
                                    Swal.fire('Gagal!', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'Terjadi kesalahan saat menghapus.', 'error');
                            }
                        });
                    }
                });
            });

            $('#selectRka').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih RKA --',
                dropdownParent: $('#modalSpj'),
                ajax: {
                    url: '/spj/rka/list',
                    dataType: 'json',
                    processResults: function (data) {

                        if (!data || data.length === 0) {
                            return {
                                results: [
                                    {
                                        id: '',
                                        text: '⚠ Tahap belum dikunci atau tidak ada RKA tersedia'
                                    }
                                ]
                            };
                        }

                        return { results: data };
                    }
                }
            });

            // === RKA DIPILIH ===
            $('#selectRka').on('change', function () {
                if (!isEditMode) {
                    $('#selectRekening').val('');
                }

                let idAnggaran = $(this).val();
                sisaPaguAktif = parseFloat($(this).find(':selected').data('sisapagu')) || 0;

                if (!idAnggaran) return;

                $.getJSON(`/spj/get-rekening/${idAnggaran}`, function (res) {
                    if (res.success) {
                        let rekeningSelect = $('#selectRekening');
                        rekeningSelect.empty().append('<option value="">-- Pilih Rekening Belanja --</option>');
                        res.data.forEach(item => {
                            rekeningSelect.append(`<option value="${item.kode_rekening}">${item.kode_rekening}</option>`);
                        });
                    }
                });
            });

            // === REKENING DIPILIH ===
            $('#selectRekening').on('change', function () {

                // 🚫 JANGAN LOAD RINCIAN SAAT EDIT!
                if (isEditMode) return;

                let idAnggaran = $('#selectRka').val();
                let idRekening = $(this).val();

                if (!idRekening) return;

                $.getJSON(`/spj/get-rincian/${idAnggaran}/${idRekening}`, function (res) {

                    let tbody = $('#tabel-rincian tbody');
                    tbody.empty();

                    if (res.success) {
                        res.data.forEach(item => {
                            tbody.append(`
                                <tr>
                                    <td>
                                        <input type="hidden" name="id_rincian_anggaran[]" value="${item.id}">
                                        <input type="text" name="nama_barang[]" class="form-control" value="${item.nama_barang}" readonly>
                                    </td>
                                    <td>
                                        <input type="hidden" name="sisa[]" value="${item.sisa}">
                                        <input type="number" name="volume[]" class="form-control volume" value="0" title="Sisa volume: ${item.sisa}" data-bs-toggle="tooltip">
                                    </td>
                                    <td><input type="text" name="satuan[]" class="form-control" value="${item.satuan}" readonly></td>
                                    <td><input type="number" name="harga[]" class="form-control harga" value="${item.harga}" data-harga-rka="${item.harga}"></td>
                                    <td class="jumlah text-end" data-value="0">0</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm hapusBaris">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });

                    // aktifkan tooltip
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    }

                    hitungTotalSPJ();
                    cekValidasiVolume();
                    cekErrorGlobal();
                    validasiHonorDenganSpj();
                });
            });
            
            function toNumber(val) {
                if (!val) return 0;

                // Jika format input harga (30000.00)
                if (/^\d+(\.\d+)?$/.test(val)) {
                    return Number(val);
                }

                // Format rupiah (30.000,00)
                return Number(
                    val.toString()
                    .replace(/\./g, '')   // 30.000 → 30000
                    .replace(/,/g, '.')   // 30000,50 → 30000.50
                ) || 0;
            }

            // 🔥 Format ke rupiah Indonesia
            function formatRupiah(num) {
                num = Number(num) || 0;
                return num.toLocaleString('id-ID');
            }

            function parseRupiah(str) {
                if (!str) return 0;

                return Number(
                    str.toString()
                    .replace(/\./g, '')   // hilangkan pemisah ribuan
                    .replace(/,/g, '.')   // ubah koma desimal ke titik
                ) || 0;
            }

            // === HITUNG JUMLAH ===
            $(document).on('input', '.volume, .harga', function () {

                let row   = $(this).closest('tr');
                let vol   = toNumber(row.find('.volume').val());
                let harga = toNumber(row.find('.harga').val());
                let sisa  = toNumber(row.find('input[name="sisa[]"]').val());
                let hargaRka = toNumber(row.find('.harga').data('harga-rka'));

                let jumlah = vol * harga;

                // 🔥 DEBUG LOG DI SINI
                console.log("VOL=", vol, "HARGA=", harga, "JUMLAH=", jumlah);

                // Tampilkan jumlah dengan aman
                row.find('.jumlah').val(formatRupiah(jumlah));
                row.find('.jumlah').text(formatRupiah(jumlah)).attr('data-value', jumlah);

                // RESET warna
                row.removeClass('table-danger table-warning');

                // VALIDASI
                if (vol > sisa) row.addClass('table-danger');
                if (harga <= 0) row.addClass('table-danger');
                if (harga > hargaRka) row.addClass('table-warning');

                hitungTotalSPJ();
                cekValidasiVolume();
                cekErrorGlobal();
                validasiHonorDenganSpj();
            });

            // === HITUNG TOTAL SPJ ===
            function hitungTotalSPJ() {
                let total = 0;

                // $('#tabel-rincian tbody tr').each(function () {
                //     // let text = $(this).find('.jumlah').val();
                //     // total += toNumber(text);
                //     let jml = Number($(this).find('.jumlah').data('value')) || 0;
                //     total += jml;
                // });

                $('#tabel-rincian tbody tr').each(function () {
                    let text = $(this).find('.jumlah').val();
                    total += toNumber(text);
                });

                $('#totalSpjText').text(formatRupiah(total));

                // $('#totalSpjText').text(formatRupiah(total));

                if (sisaPaguAktif > 0) {
                    let persen = (total / sisaPaguAktif) * 100;
                    $('#progressPagu')
                        .css('width', Math.min(persen, 100) + '%')
                        .text(Math.round(persen) + '%');
                }

                validasiHonorDenganSpj();
            }

            // CEK ERROR GLOBAL
            // =========================================
            function cekErrorGlobal() {
                let error = false;

                $('#tabel-rincian tbody tr').each(function () {
                    if ($(this).hasClass('table-danger')) error = true;
                });

                let total = parseFloat($('#totalSpjText').text().replace(/\./g,'')) || 0;
                if (total > sisaPaguAktif) error = true;

                $('#btnSimpanSpj').attr('disabled', error);
            }

            // === SUBMIT FORM ===
            $('#formSpj').on('submit', function(e) {
                e.preventDefault();

                let url = $(this).attr('action');
                let method = $(this).attr('method');

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('Berhasil', res.message, 'success');

                            // Tutup modal
                            $('#modalSpj').modal('hide');

                            // ⚡ Reset form SPJ
                            $('#formSpj')[0].reset();
                            $('#hiddenSpjId').val('');

                            // Reset select2
                            $('#jenis_kwitansi').val('').trigger('change');
                            $('#selectRka').val('').trigger('change');
                            $('#selectRekening').empty().append('<option value="">-- Pilih Rekening --</option>');
                            $('#selectRekanan').val('').trigger('change');

                            // Reset tabel honor
                            $('#tbodyHonor').html(`
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        Belum ada data. Tambah penerima dengan tombol di bawah.
                                    </td>
                                </tr>
                            `);

                            $('#totalHonorText').text('0');
                            $('#totalPajakText').text('0');
                            $('#totalDibayarText').text('0');

                            // Reset tabel barang
                            $('#tbodyBarang').html(`
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        Pilih RKA dan Rekening Belanja untuk menampilkan rincian barang.
                                    </td>
                                </tr>
                            `);

                            // Reset Total SPJ & Progress
                            $('#totalSpjText').text('0');
                            $('#progressPagu').css('width', '0%').removeClass('bg-warning bg-danger').addClass('bg-success').text('0%');

                            // Reload tabel utama
                            $('#tabelSpj').DataTable().ajax.reload();
                        }
                    }
                });
            });

            function cekValidasiVolume() {
                let adaError = false;

                $('#tabel-rincian tbody tr').each(function () {
                    if ($(this).hasClass('table-danger')) {
                        adaError = true;
                    }
                });

                if (adaError) {
                    $('#btnSimpanSpj').prop('disabled', true).hide();
                } else {
                    $('#btnSimpanSpj').prop('disabled', false).show();
                }
            }

            $(function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            });

            // === ON CHANGE JENIS KWITANSI ===
            $(document).on('change', '#jenis_kwitansi', function () {
                let val = $(this).val();

                if (val === 'honor_transport') {
                    // Disable tanggal nota
                    $('input[name="tanggal_nope"]').prop('disabled', true).val('');

                    // Tampilkan form honor
                    $('#formHonor').show();
                } else {
                    // Reset
                    $('input[name="tanggal_nope"]').prop('disabled', false);

                    // Hide form honor
                    $('#formHonor').hide();

                    // Hapus isi honor
                    $('#tbodyHonor').html(`
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Belum ada data. Tambah penerima dengan tombol di bawah.
                            </td>
                        </tr>
                    `);
                }
            });

            $(document).on('click', '#btnTambahHonor', function () {
                let tbody = $('#tbodyHonor');

                if (tbody.find('td').length === 1) tbody.empty();

                tbody.append(`
                    <tr>
                        <td><input type="text" name="honor_nama[]" class="form-control" required></td>
                        <td><input type="text" name="honor_jabatan[]" class="form-control" required></td>

                        <td><input type="number" name="honor_jumlah[]" class="form-control honorJumlah" required></td>

                        <td><input type="number" name="honor_pajak[]" class="form-control honorPajak" value="0" min="0" max="100"></td>

                        <td><input type="text" name="honor_nilai_pajak[]" class="form-control honorNilaiPajak" readonly></td>

                        <td><input type="text" name="honor_diterima[]" class="form-control honorDiterima" readonly></td>

                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger hapusHonor">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });

            $(document).on('click', '.hapusHonor', function () {
                $(this).closest('tr').remove();

                // Jika kosong, tampilkan placeholder
                if ($('#tbodyHonor tr').length === 0) {
                    $('#tbodyHonor').html(`
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Belum ada data. Tambah penerima dengan tombol di bawah.
                            </td>
                        </tr>
                    `);
                }
            });

            function hitungTotalHonor() {
                let total = 0;

                $('.honorJumlah').each(function () {
                    let v = parseFloat($(this).val()) || 0;
                    total += v;
                });

                $('#totalHonorText').text(total.toLocaleString('id-ID'));

                return total;
            }

            $(document).on('input', '.honorJumlah', function () {
                hitungTotalHonor();
                validasiHonorDenganSpj();
            });

            function validasiHonorDenganSpj() {
                let jenis = $('#jenis_kwitansi').val();

                // ===== JIKA BUKAN HONOR =====
                if (jenis !== 'honor_transport') {
                    $('#warningHonor').remove();
                    $('#btnSimpanSpj').prop('disabled', false);
                    return;
                }

                // ===== KHUSUS HONOR =====
                let totalHonor = hitungTotalHonor();
                let totalSpj = parseFloat($('#totalSpjText').text().replace(/\./g, '')) || 0;

                if (totalHonor !== totalSpj) {
                    $('#btnSimpanSpj').prop('disabled', true);

                    if ($('#warningHonor').length === 0) {
                        $('#formHonor').append(`
                            <div id="warningHonor" class="mt-2 text-danger fw-bold">
                                ⚠ Total honor harus SAMA dengan total SPJ!
                            </div>
                        `);
                    }

                } else {
                    $('#warningHonor').remove();
                    $('#btnSimpanSpj').prop('disabled', false);
                }
            }

            $('#jenis_kwitansi').on('change', function () {
                validasiHonorDenganSpj(); // re-check agar tombol tidah nyangkut disabled
            });

            function hitungHonorPerBaris(row) {
                let honor = parseFloat(row.find('.honorJumlah').val()) || 0;
                let pajak = parseFloat(row.find('.honorPajak').val()) || 0;

                let nilaiPajak = honor * pajak / 100;
                let diterima = honor - nilaiPajak;

                row.find('.honorNilaiPajak').val(nilaiPajak.toLocaleString('id-ID'));
                row.find('.honorDiterima').val(diterima.toLocaleString('id-ID'));
            }

            function hitungTotalHonorSemua() {
                let totalHonor = 0;
                let totalPajak = 0;
                let totalDibayar = 0;

                $('#tbodyHonor tr').each(function () {
                    let honor = parseFloat($(this).find('.honorJumlah').val()) || 0;
                    let pajak = parseFloat($(this).find('.honorPajak').val()) || 0;

                    let nilaiPajak = honor * pajak / 100;
                    let diterima = honor - nilaiPajak;

                    totalHonor += honor;
                    totalPajak += nilaiPajak;
                    totalDibayar += diterima;
                });

                $('#totalHonorText').text(totalHonor.toLocaleString('id-ID'));
                $('#totalPajakText').text(totalPajak.toLocaleString('id-ID'));
                $('#totalDibayarText').text(totalDibayar.toLocaleString('id-ID'));

                return totalHonor;
            }

            $(document).on('input', '.honorJumlah, .honorPajak', function () {
                let row = $(this).closest('tr');
                hitungHonorPerBaris(row);
                hitungTotalHonorSemua();
                validasiHonorDenganSpj(); // fungsi validasi lama tetap dipakai
            });

            

    });
    </script>

        <style>
        .text-muted { font-style: italic; color: #6c757d !important; }
        .table-danger input { background-color: #ffeaea; }
        .table-warning input { background-color: #fff3cd !important; }
        </style>

        