<script>
$(function () {
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

    var table = $('#tabelrka').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('rka.index') }}",
        order: [[1, 'asc']], // <-- urut berdasarkan kolom kedua
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className:'text-center'},
            {data: 'program', name: 'program'},
            {data: 'kegiatan', name: 'kegiatan'},
            {data: 'sub_kegiatan', name: 'sub_kegiatan'},
            {data: 'sumber_dana', name: 'sumber_dana', className:'text-center'},
            {data: 'pagu_anggaran', name: 'pagu_anggaran', className:'text-end'},
            // ✅ Tambahkan name & className
            {data: 'realisasi', name: 'realisasi', className:'text-end', render: $.fn.dataTable.render.number('.', ',', 0)},
            {data: 'sisa_pagu', name: 'sisa_pagu', className:'text-end', render: $.fn.dataTable.render.number('.', ',', 0)},
            {data: 'pptk', name: 'pptk'},
            {data: 'aksi', name: 'aksi', orderable: false, searchable: false, className:'text-center'},
        ]
    });

    // Tambah data
    $('#createRka').click(function () {
        $('#formRka').trigger("reset");
        $('#id').val('');
        $('#tambahRka').modal('show');
    });

    // Simpan data
    $('#formRka').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $('#saveBtn').html('Menyimpan...');

        $.ajax({
            type: 'POST',
            url: "{{ route('rka.store') }}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                $('#formRka').trigger("reset");
                $('#tambahRka').modal('hide');
                $('#saveBtn').html('Simpan');
                Swal.fire('Berhasil!', 'Data berhasil disimpan', 'success');
                table.draw();
            },
            error: function (err) {
                console.log(err);
                $('#saveBtn').html('Simpan');
            }
        });
    });

    // Edit data
    // $('body').on('click', '.editRka', function () {
    //     var id = $(this).data('id');
    //     $.get("{{ url('/rka/edit') }}/" + id, function (data) {
    //         $('#tambahRka').modal('show');
    //         $('#id').val(data.id);
    //         $('#id_subkegiatan').val(data.id_subkegiatan);
    //         $('#sumber_dana').val(data.sumber_dana);
    //         $('#pagu_anggaran').val(data.pagu_anggaran);
    //     })
    // });

    // Edit data
    $('body').on('click', '.editRka', function () {
        var id = $(this).data('id');
        $.get("{{ url('/rka/edit') }}/" + id, function (data) {
            $('#tambahRka').modal('show');
            $('#id').val(data.id);
            // $('#id_pptk').val(data.id_pptk); // 🔥 auto load PPTK
            $('#sumber_dana').val(data.sumber_dana);
            $('#pagu_anggaran').val(data.pagu_anggaran);

            // set PPTK
            if (data.id_pptk) {
                $('#id_pptk').val(data.id_pptk);
            } else {
                $('#id_pptk').val('');
            }

            // Set Select2 value
            if (data.id_subkegiatan) {
                var option = new Option(data.sub_kegiatan_text ?? 'Memuat...', data.id_subkegiatan, true, true);
                $('#id_subkegiatan').append(option).trigger('change');
            } else {
                $('#id_subkegiatan').val(null).trigger('change');
            }
        })
    });

    // Hapus data
    $('body').on('click', '.deleteRka', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('/rka/destroy') }}/" + id,
                    success: function (data) {
                        Swal.fire('Terhapus!', data.success, 'success');
                        table.draw();
                    }
                });
            }
        });
    });

    // === Inisialisasi Select2 di dalam modal ===
    function initSelect(id, url, extra = {}) {
        $(id).select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#tambahRka'),
            placeholder: 'Pilih...',
            ajax: {
                url: url,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term, ...extra };
                },
                processResults: function (data) {
                    return { results: data };
                }
            }
        });
    }

    $('#tambahRka').on('shown.bs.modal', function () {

        initSelect('#urusan', '/api/urusan');

        $('#urusan').on('change', function () {
            $('#bidang').empty();
            initSelect('#bidang', '/api/bidang', { id_urusan: this.value });
        });

        $('#bidang').on('change', function () {
            $('#program').empty();
            initSelect('#program', '/api/program', { id_bidang: this.value });
        });

        $('#program').on('change', function () {
            $('#kegiatan').empty();
            initSelect('#kegiatan', '/api/kegiatan', { id_program: this.value });
        });

        $('#kegiatan').on('change', function () {
            $('#sub_kegiatan').empty();
            initSelect('#sub_kegiatan', '/api/sub-kegiatan', { id_kegiatan: this.value });
        });
    });
    
    // === SET TAHAP AKTIF ===
    $('#btnSetAktif').click(function () {
        let tahap = $('#selectTahap').val();

        $.post("{{ route('rka.setActive') }}", { tahap: tahap }, function (res) {
            Swal.fire("Berhasil", "Tahap aktif berhasil diubah", "success");
            location.reload();
        });
    });

    $('#btnToggleLock').click(function () {
        let tahap = $('#selectTahap').val();

        $.post("{{ route('rka.toggleLock') }}", { tahap: tahap }, function (res) {
            Swal.fire("Status Berubah", res.message, "success");
            location.reload();
        });
    });
    
});
</script>
